<?php

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "../index.php?site=".$_SESSION['site_id'];	


if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
} 

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');



 $email=$_REQUEST['email'];
 if($_REQUEST['status']==1){
   
     echo $email. " Sucessfully Authenticated<br/>";
     
 }
 $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://'; 
 $category_arr= array(
        array('title' =>'patients','fieldid'=>'parent_folder'),
        array('title' =>'users','fieldid'=>'user_parent_folder'),
        array('title' =>'insurance','fieldid'=>'ins_parent_folder'),
        array('title' =>'pharmacy','fieldid'=>'pharmacy_parent_folder'),
        array('title' =>'address_Book','fieldid'=>'addrbk_parent_folder'),
        array('title' =>'facility','fieldid'=>'facility_parent_folder')
);
$link_url = "//".$_SERVER['HTTP_HOST']."/interface/patient_file/summary/link_folders_save.php";
$foulderinfo = '//' . $_SERVER['HTTP_HOST'] . '/api/DriveSync/getfileinfo_web/' . $email; 
?>
<!DOCTYPE html>
<html>  
    <head>
	<meta charset='UTF-8'>
	<title>Configuration Page</title>
        <link rel='stylesheet' type='text/css' href='../css/bootstrap-3.0.3.min.css'>
<!--        <link rel='stylesheet' type='text/css' href='../css/jquery.dataTables.css'>
        <link rel='stylesheet' type='text/css' href='../css/dataTables.tableTools.css'>
        <link rel='stylesheet' type='text/css' href='../css/dataTables.colVis.css'>
        <link rel='stylesheet' type='text/css' href='../css/dataTables.colReorder.css'>-->
        
        <script type='text/javascript' src='../js/jquery-1.11.1.min.js'></script>
<!--        <link rel="stylesheet" href="../../reports/fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
        <script type="text/javascript" src="../../reports/fancybox/source/jquery.fancybox.pack.js"></script>-->
        
	
        <script>
            $(function(){
                if($('#imap-pwd').val()==''){
                    document.getElementById('imap-pwd').disabled=false;
                }else {
                     document.getElementById('imap-pwd').disabled=true;
                }
                var $target = $('#patient-folder');
                var p1= [];
                var p2= [];
                var p3= []; var p4= []; var p5= []; var p6= []; var p7= []; var p8=[];
                $.each($(".patient:checked"),function(index,value){
                    p1.push($(value).val())
                })
                $.each($(".provider:checked"),function(index,value){
                    p2.push($(value).val())
                })
                $.each($(".patient-file:checked"),function(index,value){
                    p3.push($(value).val())
                })
                $.each($(".insurance:checked"),function(index,value){
                    p4.push($(value).val())
                })
                $.each($(".pharmacy:checked"),function(index,value){
                    p5.push($(value).val())
                })
                $.each($(".facility:checked"),function(index,value){
                    p6.push($(value).val())
                })
                 $.each($(".org:checked"),function(index,value){
                    p8.push($(value).val())
                })
                $(".patient").change(function(){
                    if(!$target.val().match($(this).val())){
                        p1.push($(this).val())
                        var text = $target.val(p1.join("_")); 
                    } else {
                        var p1index = $.inArray($(this).val(),p1)
                        p1.splice(p1index, 1);
                        $target.val(p1.join("_"));
                    }                    
                });
                
                var $target1 = $('#provider-folder');
               
                    $(".provider" ).change(function(){
                   
                    if(!$target1.val().match($(this).val())){
                        p2.push($(this).val())
                        var text = $target1.val(p2.join("_")); 
                    } else {
                        var p1index = $.inArray($(this).val(),p2)
                        p2.splice(p1index, 1);
                        $target1.val(p2.join("_"));
                    }      
                     
                });
               
               var $target2 = $('#patient-file');
                    $(".patient-file" ).change(function(){
                    if(!$target2.val().match($(this).val())){
                        p3.push($(this).val())
                        var text = $target2.val(p3.join("_")); 
                    } else {
                        var p1index = $.inArray($(this).val(),p3)
                        p3.splice(p1index, 1);
                        $target2.val(p3.join("_"));
                    }  
                    
                });
                var $target3 = $('#insurance-folder');
                    $(".insurance" ).change(function(){
                    if(!$target3.val().match($(this).val())){
                        p4.push($(this).val())
                        var text = $target3.val(p4.join("_")); 
                    } else {
                        var p1index = $.inArray($(this).val(),p4)
                        p4.splice(p1index, 1);
                        $target3.val(p4.join("_"));
                    }  
                    
                });
                
                var $target4 = $('#pharmacy-folder');
                    $(".pharmacy" ).change(function(){
                    if(!$target4.val().match($(this).val())){
                        p5.push($(this).val())
                        var text = $target4.val(p5.join("_")); 
                    } else {
                        var p1index = $.inArray($(this).val(),p5)
                        p5.splice(p1index, 1);
                        $target4.val(p5.join("_"));
                    }  
                    
                });
                 var $target5 = $('#facility-folder');
                    $(".facility" ).change(function(){
                    if(!$target5.val().match($(this).val())){
                        p6.push($(this).val())
                        var text = $target5.val(p6.join("_")); 
                    } else {
                        var p1index = $.inArray($(this).val(),p6)
                        p6.splice(p1index, 1);
                        $target5.val(p6.join("_"));
                    }  
                    
                });
//                var $target6 = $('#addrbk-folder');
//                    $(".addrbk" ).change(function(){
//                    if(!$target6.val().match($(this).val())){
//                        p7.push($(this).val())
//                        var text = $target6.val(p7.join("_")); 
//                    } else {
//                        var p1index = $.inArray($(this).val(),p7)
//                        p7.splice(p1index, 1);
//                        $target6.val(p7.join("_"));
//                    }  
//                    
//                });
                 var $target7 = $('#org-folder');
                    $(".org" ).change(function(){
                    if(!$target7.val().match($(this).val())){
                        p8.push($(this).val())
                        var text = $target7.val(p8.join("_")); 
                    } else {
                        var p1index = $.inArray($(this).val(),p8)
                        p8.splice(p1index, 1);
                        $target7.val(p8.join("_"));
                    }  
                    
                });
                
                $('#save').click(function(e){
                 
                     var isValid = document.getElementById('driveset').checkValidity();

                         if(false === isValid){
                            //allow the browser's default submit event behavior 
                            return true;
                        } 
                      
                    var errorno=0; 
                    if($('#patient-folder').val()==''){

                        alert('Please Fill patient folder name format');
                        errorno++;
                        return false;
                    }else if($('#patient-file').val()=='') {

                        alert('Please Fill patient file name format');
                        errorno++;
                         return false;
                    }else if($('#provider-folder').val()==''){

                         alert('Please Fill provider/user folder name format');
                        errorno++;
                         return false;
                    }
                    else if($('#insurance-folder').val()==''){

                         alert('Please Fill insurance folder name format');
                        errorno++;
                         return false;
                    }
                    else if($('#pharmacy-folder').val()==''){

                         alert('Please Fill Pharmacy folder name format');
                        errorno++;
                         return false;
                    }
                    else if($('#facility-folder').val()==''){

                         alert('Please Fill Facility folder name format');
                        errorno++;
                         return false;
                    }
    //                else if($('#addrbk-folder').val()==''){
    //                    
    //                     alert('Please Fill Address Book Type folder name format');
    //                    errorno++;
    //                     return false;
    //                }
                    else if($('#org-folder').val()==''){

                         alert('Please Fill Organisation folder name format');
                        errorno++;
                         return false;
                    }else if($('#instance-parent-folder').val()==''){
                         alert('Please Fill Practice folder name');
                         errorno++;
                         return false;
                    } else if($('#parent-folder').val()==''){

                         alert('Please Fill Patients folder link');
                        errorno++;
                         return false;
                    }else if($('#user-parent-folder').val()==''){
                         alert('Please Fill provider/user  folder link');
                         errorno++;
                         return false;
                    }else if($('#ins-parent-folder').val()==''){

                         alert('Please Fill Insurance folder link');
                        errorno++;
                         return false;
                    }else if($('#pharmacy-parent-folder').val()==''){
                         alert('Please Fill Pharmacy folder link');
                         errorno++;
                         return false;
                    } else if($('#addrbk-parent-folder').val()==''){

                         alert('Please Fill Address Book folder link');
                        errorno++;
                         return false;
                    }else if($('#facility-parent-folder').val()==''){
                         alert('Please Fill Facility  folder link');
                         errorno++;
                         return false;
                    }else if($('#email_parent_folder').val()==''){
                         alert('Please Fill Email Attachment  folder link');
                         errorno++;
                         return false;
                    }
                    else if($('#imap-user').val()==''){
                         alert('Please Fill imap email');
                         errorno++;
                         return false;
                    }
                    else if($('#imap-pwd').val()==''){
                         alert('Please Fill Email password');
                         errorno++;
                         return false;
                    }
                    if($('#imap-user').val()!='<?php echo $email; ?>'){
                         alert('Please Fill configured email only');
                         errorno++;
                         return false;
                    }
                     var i=0;
                    $('input:checkbox.patient').each(function () {
                       var sThisVal = (this.checked ? $(this).val() : "");
                      if(sThisVal!=''){
                           i++;
                       }
                    });
                    if(i==0){
                        alert("please Use Checkbox options only for Patient...");
                    }
                    var j=0;
                     $('input:checkbox.patient-file').each(function () {
                       var sThisVal = (this.checked ? $(this).val() : "");
                       if(sThisVal!=''){
                            j++;
                       }
                    });
                    if(j==0){
                        alert("please Use Checkbox options only for Patient File...");
                    }
                     var k=0;
                     $('input:checkbox.provider').each(function () {
                       var sThisVal = (this.checked ? $(this).val() : "");
                       if(sThisVal!=''){
                            k++;
                       }
                    });
                    if(k==0){
                        alert("please Use Checkbox options only for Provider...");
                    }

                    if(document.getElementById("ins-name").checked==false){
                        alert("please use checkbox option only for insurance..");   
                        errorno++;
                        return false;
                    }
                    if(document.getElementById("ph-name").checked==false){
                        alert("please use checkbox option only for Pharmacy..");   
                        errorno++;
                        return false;
                    }
                    if(document.getElementById("org-name").checked==false){
                        alert("please use checkbox option only for Address Book..");   
                        errorno++;
                         return false;
                    }
                    if(document.getElementById("fac-name").checked==false){
                        alert("please use checkbox option only for Facility..");   
                        errorno++;
                        return false;
                    }
                    
                    
                    if(errorno==0 && i!=0 && j!=0 && k!=0){

                        //document.driveset.submit();
                        params='';
                        for( var i=0; i<document.driveset.elements.length; i++ )
                        {
                           if(document.driveset.elements[i].id=='to_user') {
                               var fieldName = document.driveset.elements[i].id;
                           }else {
                               var fieldName = document.driveset.elements[i].name;
                           }
                           var fieldValue = $(document.driveset.elements[i]).val();
                           params += fieldName + '*' + fieldValue + '&';
                        }
                       
                        e.preventDefault();
                      //  alert(params);
                        $.ajax({
                            type: "POST",
                            url: 'driveconfig_save.php',
                            data: {params:params},
                            success: function(data) {

                                        if(data=='') { alert('sucessfully saved!!'); location.reload(); }else { alert(data); }
                            },
                        });
                    }

                });
               
            });
            function check_status(id){
              if(document.getElementById(id).checked==false){
                   document.getElementById(id).value='no'
                }else{
                     document.getElementById(id).value='yes'
                }
            }  
            function folder_creation(url,index,action){
                if(url==0){
                    alert("There is no folder to create!!!");
                }else {
                    $('#loading'+index).show();
                    $fc= $.ajax({
                        type: "POST",
                        url: url,
                        data: {action:action},
                        success: function(data) {
                        console.log(data);
                        $('#loading'+index).hide();
                        var res=data.split(":");
                        //alert(res[0]+"=="+res[1]);
                        if(action=='change'){
                            if(res[1]=='cont') {
                                 document.getElementById('#ful'+index).style.display = "block";
                                 if(res[0]!='internal server error'){
                                 $('#ful'+index).html(res[0]);
                                }else {alert(res[0]);}
                                 folder_creation(url,index,action); 
                            }else {
                                 document.getElementById('#ful'+index).style.display = "none";
                                  if(res[0]!='internal server error'){
                                    $('#ful'+index).html(res[0]);
                                  }else {alert(res[0]);}
                                 alert(res[1]);
                            } 
                        }else{
                            if(res[1]=='cont') {
                                 if(res[0]!='internal server error' && res[0]!='user limit exceed'){
                                  $('#cnt'+index).html(res[0]);
                                }else {alert(res[0]);}
                               
                                 folder_creation(url,index,action);  
                            }else {
                                if(res[0]!='internal server error' && res[0]!='user limit exceed'){
                                 $('#cnt'+index).html(res[0]);
                                }else {alert(res[0]);}
                                 alert(res[1]);
                                 
                            } 
                        }          
                                 
                    },error: function (error) {
                        $('#loading'+index).hide();
                       alert("Some error occured please run again");
                       
                    }
                });
                }
            }
            function msg_creation(url,category,num){
                 $('#'+category+num).show();
                 $.ajax({
                    type: "GET",
                    url:url ,
                    data: {category:category},
                    success: function(data) {
                        var res=data.split(":");
                        
                       
                        if(res[1]=='cont') {
                                 if(res[0]!='internal server error' && res[0]!='user limit exceed'){
                                  $('#'+category+'_'+num).html(res[0]);
                                }else {alert(res[0]);}
                               
                                 msg_creation(url,category,num);  
                            }else {
                                if(res[0]!='internal server error' && res[0]!='user limit exceed'){
                                $('#'+category+'_'+num).html(res[0]);
                                }else {alert(res[0]);}
                                 alert(res[1]);
                                 
                            } 
                         $('#'+category+num).hide();
                           // alert(data);
                    },error: function (error) {
                         $('#'+category+num).hide();
                       alert("Some error occured please run again");
                    }
                });
            }
            function store_email(list_of_ids){
                
                $('#attach').show();
                var url='<?php echo $protocol; ?>'+'<?php echo $_SERVER['HTTP_HOST']; ?>'+'/api/DriveSync/storeEmailAttachment_web/'+$('#imap-user').val()+'/pwd/'+list_of_ids;
                console.log(url);
                $.ajax({
                    type: "GET", 
                    url:url ,
                    data: {},
                    success: function(data) {
                             $('#attach').hide();
                              var res1=data.split(":");
                              if(res1[1]=='cont'){ 
                                  $('#cntmail').html(res1[0]);
                                  alert(list_of_ids);
                                  store_email(list_of_ids);
                              }
                              else{  
                                  $('#cntmail').html(res1[0]);
                                  alert(res1[1]);
                              }
                             
                            
                    },error: function (error) {
                         $('#attach').hide();
                       alert("Some error occured please run again");
                    }
                });
            }
            function change_pwd(){
                
                if(document.getElementById("ch_pwd").checked==true){
                    document.getElementById('imap-pwd').disabled=false;
                    document.getElementById('imap-pwd').value = "";
                }else if(document.getElementById('imap-pwd').value==''){
                     document.getElementById('imap-pwd').disabled=false;
                }else {
                    document.getElementById('imap-pwd').disabled=true;
                }
            }
            $(".fancybox").fancybox({
                openEffect  : 'none',
                closeEffect : 'none',
                iframe : {
                        preload: false
                }
            });

            $(".various").fancybox({
                maxWidth	: 200,
                maxHeight	: 200,
                fitToView	: false,
                width		: '70%',
                height		: '70%',
                autoSize	: false,
                closeClick	: false,
                openEffect	: 'none',
                closeEffect	: 'none'

            });

            
            $('.fancybox-media').fancybox({
                openEffect  : 'none',
                closeEffect : 'none',
                helpers : {
                        media : {}
                }
            });
        </script>
        <style>
        div.DTTT_container {
                float: none;
        }
        </style>
        
    </head>
<body>
    <?php
        function message_count($mail,$category){ 
             $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://'; 
             $category_arr= array(
                    array('title' =>'patients','fieldid'=>'parent_folder'),
                    array('title' =>'users','fieldid'=>'user_parent_folder'),
                    array('title' =>'insurance','fieldid'=>'ins_parent_folder'),
                    array('title' =>'pharmacy','fieldid'=>'pharmacy_parent_folder'),
                    array('title' =>'address_Book','fieldid'=>'addrbk_parent_folder'),
                    array('title' =>'facility','fieldid'=>'facility_parent_folder')
            );
            $selection = sqlStatement("select * from tbl_drivesync_authentication where email='" .$mail . "'");
            $sel_rows = sqlFetchArray($selection);
            foreach ($category_arr as $key => $item) {
                $idvalue = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows[$item['fieldid']]);
                if($category_arr[$key]['title']==$category){
                 $folderid = $idvalue;
                }
            }

            $curl = curl_init();
            $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/listall_folderid/'.$mail.'/'. $folderid.'/all';
            curl_setopt($curl,CURLOPT_URL, $form_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
            $result1 = curl_exec($curl);
            $resultant1 = $result1;
            curl_close($curl);
            $all_folders = json_decode($resultant1, TRUE);
            $i=0; $k=0; 

            $result12=array();
            $list_sql=sqlStatement("select DISTINCT(doc_links)  from tbl_pnotes_file_relation where type='$category'");
            while($data_row=sqlFetchArray($list_sql)){
                $id=str_replace('https://drive.google.com/drive/folders/','',$data_row['doc_links']);
                $result12[$i]=$id;  
                $i++;
            }
            
            $tktid=array_diff($all_folders,$result12);
            
            return count($tktid);
        }
        $selection=sqlStatement("select * from tbl_drivesync_authentication where email='$email' order by id desc");
        $sel_rows=sqlFetchArray($selection);
        
    ?>
    <div class="container">
        <h2>Allcare Document Management Configuration</h2>
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#settings">Settings</a></li>
          <li><a data-toggle="tab" href="#history">History</a></li>
          <li><a data-toggle="tab" href="#users">Users</a></li>
          <li><a data-toggle="tab" href="#folder_crt">Folder Creation</a></li>
          <li><a data-toggle="tab" href="#messages">Messages </a></li>
        </ul>
        <div class="tab-content">
            <div id="settings" class="tab-pane fade in active">
                 <form name="driveset" id="driveset" method="post" action="" >
                     <div class="panel-group" id="accordion" style="padding:20px;">
                         <div class="" id="ac1">
                            <a href="#" class="aclink" data-contenar="ac1contenar">
                                <span class="sign" href="#" data-parent=""><i class="glyphicon glyphicon-plus"></i></span>
                                <span class="lbl">Files and Folder Settings</span>                      
                            </a>
                         </div>
                         <div id="ac1contenar">
                                <div class="panel panel-default">
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#instance">
                                   <h4 class="panel-title">Practice</h4>
                                 </div>
                                 <div id="instance" class="panel-collapse collapse  in">
                                     <div class="panel-body">
                                          <table>
                                               <tr>
                                                   <td><div class="inline">Parent Folder Link:</div><div class="inline"><input type="url" name="instance-parent-folder" id="instance-parent-folder" value="<?php echo $sel_rows['practice_parent_folder']; ?>" style="width:190px;"/></div></td>
                                                </tr> 
                                           </table>
                                     </div>
                                 </div>
                               </div>
                               <div class="panel panel-default" >
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#patients">
                                   <h4 class="panel-title">Patients</h4>
                                 </div>
                                 <div id="patients" class="panel-collapse collapse">
                                   <div class="panel-body"> 
                                       <table>
                                       <tr>
                                           <td> <div class="inline">Patients Parent Folder Link:</div><div class="inline"><input type="url" name="parent-folder" id="parent-folder" value="<?php echo $sel_rows['parent_folder']; ?>" style="width:190px;"/></div></td>
                                       </tr> 
                                       <tr>
                                           <?php
                                                 $sel_attr=explode("_",$sel_rows['patient_folder_format']);
                                           ?>
                                           <td> <div class="inline">Patient Folder Name Format:</div><div class="inline"><input type="text" name="patient-folder" id="patient-folder" value="<?php echo $sel_rows['patient_folder_format']; ?>" style="width:190px;" /></div></td>
                                            <td>(Select combination of this attributes to generate patient folder name format:)</td>
                                            <td>
                                                <input type="checkbox" class="patient"  id="pfname"    value="fname"  <?php if(in_array('fname',$sel_attr)) { echo "checked"; }?>/>Patient First Name<br/>
                                                <input type="checkbox" class="patient"  id="plname"    value="lname"  <?php if(in_array('lname',$sel_attr)) { echo "checked"; }?> />Patient Last Name<br/>
                                                <input type="checkbox" class="patient"  id="pid"       value="pid"    <?php if(in_array('pid',$sel_attr))   { echo "checked"; }?> />Patient ID<br/>
                                                <input type="checkbox" class="patient"  id="DOB"       value="DOB"    <?php if(in_array('DOB',$sel_attr))   { echo "checked"; }?> />Date of Birth<br/>

                                            </td>
                                       </tr>
                                        <tr>
                                           <?php
                                                 $sel_attr1=explode("_",$sel_rows['patient_file_format']);
                                           ?>
                                            <td><div class="inline">Patient File Name Format:</div><div class="inline"><input type="text" name="patient-file" id="patient-file" value="<?php echo $sel_rows['patient_file_format']; ?>" style="width:200px;"/></div></td>
                                            <td>(Select combination of this attributes to generate patient file name format:)</td>
                                            <td>
                                                <input type="checkbox" class="patient-file"  id="pfname" value="fname"   <?php if(in_array('fname',$sel_attr1)) { echo "checked"; }?>/>Patient First Name<br/>
                                                <input type="checkbox" class="patient-file"  id="plname" value="lname"   <?php if(in_array('lname',$sel_attr1)) { echo "checked"; }?> />Patient Last Name<br/>
                                                <input type="checkbox" class="patient-file"  id="pid"    value="pid"     <?php if(in_array('pid',$sel_attr1))   { echo "checked"; }?> />Patient ID<br/>
                                                <input type="checkbox" class="patient-file"  id="DOB"    value="DOB"     <?php if(in_array('DOB',$sel_attr1))   { echo "checked"; }?> />Date of Birth<br/>
                                                <input type="checkbox" class="patient-file"  id="dos"    value="dos"     <?php if(in_array('dos',$sel_attr1))   { echo "checked"; }?> />Date Of Service<br/>
                                            </td>
                                       </tr>
                                       <tr><td>
                                            <input type="checkbox"   id="auto-trigger"   name="auto-trigger" value="yes"     <?php if($sel_rows['patient_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('auto-trigger');"/>Patient Folder Creation (while new patient created) <br/>
                                           </td><td> <input type="checkbox"   id="psubfolder"   name="psubfolder" value="yes"     <?php if($sel_rows['patient_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('psubfolder');"/>Patient SubFolder Creation <br/></td></tr>
                                       </table>
                                   </div>
                                 </div>
                               </div>
                               <div class="panel panel-default" >
                                   <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#users-settings">
                                       <h4 class="panel-title">Users</h4>
                                   </div>
                                   <div id="users-settings" class="panel-collapse collapse">
                                       <div class="panel-body">
                                           <table>
                                               <tr>
                                                   <td><div class="inline">Users Parent Folder Link:</div><div class="inline"><input type="url" name="user-parent-folder" id="user-parent-folder" value="<?php echo $sel_rows['user_parent_folder']; ?>" style="width:190px;"/></div></td>
                                                </tr> 
                                               <tr>
                                               <?php 
                                                     $sel_attr2=explode("_",$sel_rows['provider_folder_format']);
                                               ?>
                                               <td><div class="inline">Users Folder Name Format:</div><div class="inline"><input type="text" name="provider-folder" id="provider-folder" value="<?php echo $sel_rows['provider_folder_format']; ?>" style="width:200px;"/></div></td>
                                                <td>(Select combination of this attributes to generate provider folder name format:)</td>
                                                <td>
                                                    <input type="checkbox" class="provider"  id="username"     value="username" <?php if(in_array('username',$sel_attr2)) { echo "checked"; }?> />User name<br/>
                                                    <input type="checkbox" class="provider"  id="pfname"       value="fname"    <?php if(in_array('fname',$sel_attr2))    { echo "checked"; }?>/>Provider First Name<br/>
                                                    <input type="checkbox" class="provider"  id="plname"       value="lname"    <?php if(in_array('lname',$sel_attr2))    { echo "checked"; }?> />Provider Last Name<br/>
                                                    <input type="checkbox" class="provider"  id="pid"          value="npi"      <?php if(in_array('npi',$sel_attr2))      { echo "checked"; }?> />NPI<br/>
                                                </td>
                                               </tr>
                                               <tr>
                                                   <td>
                                                       <input type="checkbox"   id="user-auto-trigger"   name="user-auto-trigger" value=""     <?php if($sel_rows['provider_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('user-auto-trigger');"/>User Folder Creation (while new patient created) <br/>
                                                   </td>
                                                   <td>
                                                       <input type="checkbox"   id="provider-sub-folder"   name="provider-sub-folder" value=""     <?php if($sel_rows['provider_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('provider-sub-folder');"/>User SubFolder Creation  <br/>
                                                   </td>
                                               </tr>
                                           </table>
                                       </div>
                                   </div>
                               </div>
                               <div class="panel panel-default" >
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#insurance">
                                   <h4 class="panel-title">Insurance</h4>
                                 </div>
                                 <div id="insurance" class="panel-collapse collapse">
                                     <div class="panel-body">
                                          <table>
                                               <tr>
                                                   <td><div class="inline">Insurance Parent Folder Link:</div><div class="inline"><input type="url" name="ins-parent-folder" id="ins-parent-folder" value="<?php echo $sel_rows['ins_parent_folder']; ?>" style="width:190px;"/></div></td>
                                               </tr> 
                                               <tr>
                                                   <?php 
                                                         $sel_attr3=explode("_",$sel_rows['insurance_folder_format']);
                                                   ?>
                                                   <td><div class="inline">Insurance Folder Name Format:</div><div class="inline"><input type="text" name="insurance-folder" id="insurance-folder" value="<?php echo $sel_rows['insurance_folder_format']; ?>" style="width:200px;"/></div></td>
                                                    <td>(Select combination of this attributes to generate insurance folder name format:)</td>
                                                    <td>
                                                        <input type="checkbox" class="insurance"  id="ins-name"      value="name" <?php if(in_array('name',$sel_attr3)) { echo "checked"; }?> />name<br/>

                                                    </td>
                                               </tr>
                                               <tr>
                                                   <td>
                                                       <input type="checkbox"   id="ins-auto-trigger"   name="ins-auto-trigger" value="yes"     <?php if($sel_rows['ins_folder_trigger']=='yes')   { echo "checked"; }?> />Insurance Folder Creation (while new Insurance Company created) <br/>
                                                   </td>
                                                   <td>
                                                       <input type="checkbox"   id="insurance-sub-folder"   name="insurance-sub-folder" value="yes"     <?php if($sel_rows['insurance_sub_folder']=='yes')   { echo "checked"; }?> />Insurance SubFolder Creation  <br/>
                                                   </td>
                                               </tr>
                                           </table>
                                     </div>
                                 </div> 
                               </div>
                                <div class="panel panel-default">
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#pharmacy">
                                   <h4 class="panel-title">Pharmacy</h4>
                                 </div>
                                 <div id="pharmacy" class="panel-collapse collapse">
                                     <div class="panel-body">
                                        <table>
                                           <tr>
                                               <td><div class="inline">Pharmacy Parent Folder Link:</div><div class="inline"><input type="url" name="pharmacy-parent-folder" id="pharmacy-parent-folder" value="<?php echo $sel_rows['pharmacy_parent_folder']; ?>" style="width:190px;"/></div></td>
                                           </tr> 
                                           <tr>
                                               <?php 
                                                     $sel_attr4=explode("_",$sel_rows['pharmacy_folder_format']);
                                               ?>
                                               <td><div class="inline">Pharmacy Folder Name Format:</div><div class="inline"><input type="text" name="pharmacy-folder" id="pharmacy-folder" value="<?php echo $sel_rows['pharmacy_folder_format']; ?>" style="width:200px;"/></div></td>
                                                <td>(Select combination of this attributes to generate pharmacy folder name format:)</td>
                                                <td>
                                                    <input type="checkbox" class="pharmacy"  id="ph-name"      value="name" <?php if(in_array('name',$sel_attr4)) { echo "checked"; }?> />name<br/>
                                                </td>
                                           </tr>
                                           <tr>
                                               <td>
                                                   <input type="checkbox"   id="ph-auto-trigger"   name="ph-auto-trigger" value="yes"     <?php if($sel_rows['pharmacy_folder_trigger']=='yes')   { echo "checked"; }?> />Pharmacy Folder Creation (while new Pharmacy  created) <br/>
                                               </td>
                                               <td>
                                                   <input type="checkbox"   id="pharmacy-sub-folder"   name="pharmacy-sub-folder" value="yes"     <?php if($sel_rows['pharmacy_sub_folder']=='yes')   { echo "checked"; }?> />Pharmacy SubFolder Creation  <br/>
                                               </td>
                                           </tr>
                                           </table>
                                     </div>
                                 </div>
                               </div>
                                <div class="panel panel-default" >
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#address_book">
                                   <h4 class="panel-title">Address Book</h4>
                                 </div>
                                 <div id="address_book" class="panel-collapse collapse">
                                     <div class="panel-body">
                                          <table>
                                               <tr>
                                                   <td><div class="inline">Address Book  Parent Folder Link:</div><div class="inline"><input type="url" name="addrbk-parent-folder" id="addrbk-parent-folder" value="<?php echo $sel_rows['addrbk_parent_folder']; ?>" style="width:190px;"/></div></td>
                                                </tr> 
       <!--                                        <tr>
                                               <?php 
                                                     //$sel_attr5=explode("_",$sel_rows['addrbk_folder_format']);
                                               ?>
                                               <td><div class="inline">Address book type Folder Name Format:</div><div class="inline"><input type="text" name="addrbk-folder" id="addrbk-folder" value="<?php echo $sel_rows['addrbk_folder_format']; ?>" style="width:200px;"/></div></td>
                                                <td>(Select combination of this attributes to generate provider folder name format:)</td>
                                                <td>
                                                    <input type="checkbox" class="addrbk"  id="addrbk-name"      value="abook_type" <?php if(in_array('abook_type',$sel_attr5)) { echo "checked"; }?> />Address Book Type<br/>

                                                </td>
                                               </tr>-->
                                               <tr>
                                               <?php 
                                                     $sel_attr6=explode("_",$sel_rows['org_folder_format']);
                                               ?>
                                                   <td><div class="inline">Organisation Folder Name Format:</div><div class="inline"><input type="text" name="org-folder" id="org-folder" value="<?php echo $sel_rows['org_folder_format']; ?>" style="width:200px;"/></div></td>
                                                    <td>(Select combination of this attributes to generate address book folder name format:)</td>
                                                    <td>
                                                        <input type="checkbox" class="org"  id="org-name"      value="organization" <?php if(in_array('organization',$sel_attr6)) { echo "checked"; }?> />Address Book Type<br/>

                                                    </td>
                                               </tr>
                                               <tr>
                                                   <td>
                                                   <input type="checkbox"   id="addr-auto-trigger"   name="addr-auto-trigger" value=""     <?php if($sel_rows['addrbk_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('addr-auto-trigger');"/>Address Book Folder Creation (while new Organisation  created) <br/>
                                                   </td>
                                                   <td>
                                                       <input type="checkbox"   id="addrbk-sub-folder"   name="addrbk-sub-folder" value=""     <?php if($sel_rows['addrbk_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('addrbk-sub-folder');"/>Address Book  SubFolder Creation  <br/>
                                                   </td>
                                               </tr>
                                           </table>
                                     </div>
                                 </div>
                               </div>
                                <div class="panel panel-default" >
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#facility">
                                   <h4 class="panel-title">Facility</h4>
                                 </div>
                                 <div id="facility" class="panel-collapse collapse">
                                     <div class="panel-body">
                                         <table>
                                               <tr>
                                                   <td><div class="inline">Facility Parent Folder Link:</div><div class="inline"><input type="url" name="facility-parent-folder" id="facility-parent-folder" value="<?php echo $sel_rows['facility_parent_folder']; ?>" style="width:190px;"/></div></td>
                                                </tr> 
                                               <tr>
                                               <?php 
                                                     $sel_attr7=explode("_",$sel_rows['facility_folder_format']);
                                               ?>
                                               <td><div class="inline">Facility Folder Name Format:</div><div class="inline"><input type="text" name="facility-folder" id="facility-folder" value="<?php echo $sel_rows['facility_folder_format']; ?>" style="width:200px;"/></div></td>
                                                <td>(Select combination of this attributes to generate facility folder name format:)</td>
                                                <td>
                                                    <input type="checkbox" class="facility"  id="fac-name"      value="name" <?php if(in_array('name',$sel_attr7)) { echo "checked"; }?> />name<br/>

                                                </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                    <input type="checkbox"   id="facility-folder-trigger"   name="facility-folder-trigger" value=""     <?php if($sel_rows['facility_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('facility-folder-trigger');"/>Facility Creation (while new Organisation  created) <br/>
                                                   </td>
                                                   <td>
                                                       <input type="checkbox"   id="facility-sub-folder"   name="facility-sub-folder" value=""     <?php if($sel_rows['facility_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('facility-sub-folder');"/>Facility  SubFolder Creation  <br/>
                                                   </td>
                                                </tr>
                                           </table>
                                     </div>
                                 </div>
                               </div>
                                <div class="panel panel-default" >
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#attachment">
                                   <h4 class="panel-title">Email Attachment</h4>
                                 </div>
                                 <div id="attachment" class="panel-collapse collapse">
                                     <div class="panel-body">
                                         <table>
                                               <tr>
                                                   <td><div class="inline">Parent Folder Link:</div><div class="inline"><input type="url" name="email-parent-folder" id="email-parent-folder" value="<?php echo $sel_rows['email_parent_folder']; ?>" style="width:190px;"/></div></td>
                                                </tr> 
                                                <tr><td>IMAP Configuration:</td></tr>
                                                 <tr>
                                                   <td><div class="inline" >Username:</div><div class="inline"><input type="text" name="imap-user" id="imap-user" value="<?php echo $sel_rows['imap_email']; ?>" style="width:190px;"/></div></td>
                                                   <td><div class="inline">Password:</div><div class="inline"><input type="text" name="imap-pwd" id="imap-pwd" value="<?php echo str_replace("/","@",$sel_rows['imap_pwd']); ?>" style="width:190px;"/></div></td>
                                                </tr> 
                                                <tr>
                                                   <td><?php $sql=sqlStatement("select * from tbl_user_custom_attr_1to1 where email='$email'"); 
                                                             $userid=sqlFetchArray($sql); 
                                                             if($userid['userid']==$_SESSION['authId']){  echo "<input type='checkbox' id='ch_pwd' name='ch_pwd' value='' onclick='change_pwd()' enabled/>Change Password"; }else { echo "<input type='checkbox' id='ch_pwd' name='ch_pwd' value='' onclick='change_pwd()' disabled/>Change Password";}?></td>
                                                </tr> 
                                                <tr>

                                                    <td> Select users to send Email and Emr messages:<?php 
                                               $store= unserialize($sel_rows['store_email_settings']);
                                               $store_user=explode(",",unserialize($store['user'])); 
                                               $ures = sqlStatement("SELECT u.* FROM  users u INNER JOIN `tbl_user_custom_attr_1to1` c on c.userid=u.id where  active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) 
                                                        and fname!='' and lname!='' and username!='' and (c.email!='' or u.email)  ORDER BY lname, fname");
                                               ?>
                                               <select name='store_user[]' id='store_user' multiple title=''>
                                                   <option value='' selected>select</option>
                                                       <?php 
                                                           while ($urow = sqlFetchArray($ures)) {
                                                               $uname = htmlspecialchars( $urow['fname'] . ' ' . $urow['lname'], ENT_NOQUOTES);
                                                               $optionId = htmlspecialchars( $urow['id'], ENT_QUOTES); 
                                                               echo "<option value='$optionId'"; 
                                                                   foreach($store_user as $val1) {
                                                                       if ($urow['id'] == $val1) echo "selected"; 
                                                                   }
                                                               echo ">$uname</option>";}
                                                       ?>
                                               </select></td><td>
                                               Default Text:<textarea name="store_text" id="store_text"><?php echo $store['text']; ?></textarea></td>
                                                </tr> 
                                           </table>
                                     </div>
                                 </div>
                               </div>
                         </div>
                         <div class="" id="ac2">
                            <a href="#" class="aclink" data-contenar="ac2contenar">
                                <span class="sign" href="#" data-parent=""><i class="glyphicon glyphicon-plus"></i></span>
                                <span class="lbl">Email and Message Settings</span>                      
                            </a>
                         </div>
                         <div id="ac2contenar">
                                <div class="panel panel-default" >
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#upload_setting">
                                   <h4 class="panel-title">Upload</h4>
                                 </div>
                                 <div id="upload_setting" class="panel-collapse collapse">
                                     <div class="panel-body">
                                       Select users to send Email and Emr messages:<?php 
                                               $upload= unserialize($sel_rows['upload_settings']);
                                               $upload_user=explode(",",unserialize($upload['user'])); 
                                               $ures = sqlStatement("SELECT u.* FROM  users u INNER JOIN `tbl_user_custom_attr_1to1` c on c.userid=u.id where  active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) 
                                                        and fname!='' and lname!='' and username!='' and (c.email!='' or u.email)  ORDER BY lname, fname");
                                               ?>
                                               <select name='upload_user[]' id='upload_user' multiple title=''>
                                                   <option value='' selected>select</option>
                                                       <?php 
                                                           while ($urow = sqlFetchArray($ures)) {
                                                               $uname = htmlspecialchars( $urow['fname'] . ' ' . $urow['lname'], ENT_NOQUOTES);
                                                               $optionId = htmlspecialchars( $urow['id'], ENT_QUOTES); 
                                                               echo "<option value='$optionId'"; 
                                                                   foreach($upload_user as $val1) {
                                                                       if ($urow['id'] == $val1) echo " selected"; 
                                                                   }
                                                               echo ">$uname</option>";}
                                                       ?>
                                               </select><br><br>
                                               Default Text:<textarea name="upload_text" id="upload_text" ><?php echo $upload['text']; ?></textarea><br><br>
                                               <input type="checkbox" class="upload_attach"  id="upload_attach"  name="upload_attach"  value="" <?php if($upload['attach']=='yes') echo "checked"; ?> onclick="check_status('upload_attach');"/>Do you want attachments for Email<br/>
                                      </div>
                                 </div>
                               </div>
                                <div class="panel panel-default">
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#download_setting">
                                   <h4 class="panel-title">Download</h4>
                                 </div>
                                 <div id="download_setting" class="panel-collapse collapse">
                                     <div class="panel-body">
                                          Select users to send Email and Emr messages:<?php 
                                               $download=unserialize($sel_rows['download_settings']);
                                               $download_user=explode(",",unserialize($download['user'])); 
                                               $ures = sqlStatement("SELECT u.* FROM  users u INNER JOIN `tbl_user_custom_attr_1to1` c on c.userid=u.id where  active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) 
                                                        and fname!='' and lname!='' and username!='' and (c.email!='' or u.email)  ORDER BY lname, fname");
                                               ?>
                                               <select name='download_user[]' id='download_user' multiple title=''>
                                                   <option value='' selected>select</option>
                                                       <?php 
                                                           while ($urow = sqlFetchArray($ures)) {
                                                               $uname = htmlspecialchars( $urow['fname'] . ' ' . $urow['lname'], ENT_NOQUOTES);
                                                               $optionId = htmlspecialchars( $urow['id'], ENT_QUOTES); 
                                                               echo "<option value='$optionId'"; 
                                                                   foreach($download_user as $val1) {
                                                                       if ($urow['id'] == $val1) echo "selected"; 
                                                                   }
                                                               echo ">$uname</option>";}
                                                       ?>
                                               </select><br><br>
                                               Default Text:<textarea name="download_text" id="download_text" ><?php echo $download['text']; ?></textarea><br><br>
       <!--                                        <input type="checkbox" class="download_attach"  id="download_attach"  name="download_attach"  value="" <?php if($download['attach']=='yes') echo "checked"; ?> onclick="check_status('download_attach');"/>Do you want attachments for Email<br/>-->
                                     </div>
                                 </div>
                               </div>
                                 <div class="panel panel-default">
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#move_setting">
                                   <h4 class="panel-title">Move Files</h4>
                                 </div>
                                 <div id="move_setting" class="panel-collapse collapse">
                                     <div class="panel-body">
                                          Select users to send Email and Emr messages:<?php 
                                               $move=unserialize($sel_rows['movefile_settings']);
                                               $move_user=explode(",",unserialize($move['user'])); 
                                               $ures = sqlStatement("SELECT u.* FROM  users u INNER JOIN `tbl_user_custom_attr_1to1` c on c.userid=u.id where  active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) 
                                                        and fname!='' and lname!='' and username!='' and (c.email!='' or u.email)  ORDER BY lname, fname");
                                               ?>
                                               <select name='move_user[]' id='move_user' multiple title=''>
                                                   <option value='' selected>select</option>
                                                       <?php 
                                                           while ($urow = sqlFetchArray($ures)) {
                                                               $uname = htmlspecialchars( $urow['fname'] . ' ' . $urow['lname'], ENT_NOQUOTES);
                                                               $optionId = htmlspecialchars( $urow['id'], ENT_QUOTES); 
                                                               echo "<option value='$optionId'"; 
                                                                   foreach($move_user as $val1) {
                                                                       if ($urow['id'] == $val1) echo "selected"; 
                                                                   }
                                                               echo ">$uname</option>";}
                                                       ?>
                                               </select><br><br>
                                               Default Text:<textarea name="move_text" id="move_text" ><?php echo $move['text']; ?></textarea><br><br>
       <!--                                        <input type="checkbox" class="download_attach"  id="download_attach"  name="download_attach"  value="" <?php if($download['attach']=='yes') echo "checked"; ?> onclick="check_status('download_attach');"/>Do you want attachments for Email<br/>-->
                                     </div>
                                 </div>
                               </div>
                        </div>
                    </div> 
                    
                     <input type="hidden" name="email"  id="email"  value="<?php echo $email; ?>" />
                     <input type="submit" id="save" name="save" value="Save" class="btn btn-primary" />
                     </form> 
            </div>
            <div id="history" class="tab-pane fade">
              <?php
              echo "<h3>drive sync history</h3>";
                $sql=sqlStatement("select * from DriveSync_log where email='$email' order by id desc limit 0,100");
    
                echo "<table border='1' id='patient_data' class='display'><thead><tr><th style='padding: 15px;'>Email</th><th style='padding: 15px;'>Date</th><th style='padding: 15px;'>User</th><th> ID </th><th style='padding: 15px;'>Google FolderLink</th><th>File Name</th><th style='padding: 15px;'>Status</th><th style='padding: 15px;'>WatsID</th></tr></thead>";
                while($data_row=sqlFetchArray($sql)){
                    echo "<tr ><td style='padding: 15px;'>".$data_row['email']."</td>";
                    echo "<td style='padding: 15px;'>".$data_row['date']."</td>";
                    echo "<td style='padding: 15px;'>".$data_row['user']."</td>";
                    echo "<td style='padding: 15px;'>".$data_row['patient_id']."</td>";
                    if(strpos($data_row['google_folder'], '||')==false){
                         echo "<td style='padding: 15px;'><a href=".$data_row['google_folder']." target='_blank'>".$data_row['google_folder']."</a></td>";
                    }
                    else{
                       $arr= explode("||",$data_row['google_folder']);
                        echo "<td style='padding: 15px;'>";
                       foreach($arr as $linkval){
                          echo"<a href=".$linkval." target='_blank'>".$linkval."</a>"; echo "<br>";
                       }
                       echo "</td>";
                    }
                   
                    echo "<td style='padding: 15px;'>".$data_row['file_name']."</td>";
                    echo "<td style='padding: 15px;'>".$data_row['status']."</td>";
                    if($data_row['watsID']!=0)
                    echo "<td style='padding: 15px;'><a href ='https://devint.coopsuite.com/wp-admin/post.php?post=".$data_row['watsID']."&action=edit' target='_blank'>".$data_row['watsID']."</a></td></tr>";
                    else
                    echo "<td style='padding: 15px;'>".$data_row['watsID']."</a></td></tr>";
                }
                echo"</table>"; ?>
            </div>
            <div id="users" class="tab-pane fade"> 
                <div id="driveusers">
                  <div class="btn-group btn-group-justified">
                    <a href="#" data-type="emr" class="btn btn-primary">Emr Users</a>
                    <a href="#" data-type="patients" class="btn btn-primary">Patient Users</a>
                    <a href="#" data-type="agencies" class="btn btn-primary">Agency Users</a>
                  </div>
                </div>
                <div id="userscontenar">
                    <div>
                    </div>
                </div>
            </div>
            <div id="folder_crt" class="tab-pane fade"> 
                <h3>Folder Creation</h3>
                <?php  //Drive access
                   $sql_vis=sqlStatement("SELECT drive_access from tbl_user_custom_attr_1to1 where userid='".$_SESSION['authId']."'");
                   $row1_vis=sqlFetchArray($sql_vis);
                   $avail3 = [];
                   if(!empty($row1_vis)) { 
                       $avail3=explode("|",$row1_vis['drive_access']);
                   } 
                ?>
                <div class="panel-group" id="accordion" style="padding:20px;">
                     <?php  if(in_array('patient',$avail3)) { ?><div class="panel panel-default" >
                      <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#patient_fc">
                        <h4 class="panel-title">Patients</h4>
                      </div>
                      <div id="patient_fc" class="panel-collapse collapse"><div id='loading1' style='display:none; '></div>
                          <div class="panel-body">
                               <table>
                                    <tr>
                                        <td> 
                                            <?php 
                                                $cnt=0; $pat=0;
                                                $ph=sqlStatement("select * from patient_data");
                                                while($urow2=sqlFetchArray($ph)){
                                                  $ph1=sqlStatement("select patient_folder from patient_data where pid='".$urow2['pid']."' and patient_folder='' ");
                                                  $urow2=sqlFetchArray($ph1);
                                                  if($urow2['patient_folder']==''|| empty($urow2)){
                                                    $cnt++;
                                                  }
                                                }
                                                echo "Count of Patients need to create folder:<div id='cnt1'>".$cnt."</div>";
                                                if($cnt!=0) { ?>
                                                    <a href="#" class="btn btn-primary" onclick="folder_creation('patient_folder_creation.php','1','create');">Run</a> <br>
                                                <?php } elseif ($cnt==0) { ?>
                                                    <a href="#" class="btn btn-primary" onclick="folder_creation('0','1','create');">Run</a> 
                                                <?php } 
//                                                $selection1 = sqlStatement("select * from tbl_drivesync_authentication where email='" .$email."' and folder_updation_status!=1 order by id desc");
//                                                $sel_rows1 = sqlFetchArray($selection1);
//                                                if($sel_rows1['status']=='updated'){
//                                                echo "Count of Users need to update folder:<div id='ful2'>".$pat."</div>";
//                                                ?>
<!--                                                    <input type="button"  class="btn btn-primary" name="folder_update" id="folder_update"  value="update" onclick="folder_creation('user_folder_creation.php','2','change');"/>-->
                                                        <?php //} ?>
<!--                                                From:<input type="text" name="from-limit4" id="from-limit4" value="0">-->
<!--                                            Limit:<input type="number" min='0' max='<?php echo $cnt1; ?>'name="to-limit7" id="to-limit7" value=""><br>-->
                                           
                                                
                                               
                                            </td>
                                     </tr> 
                                </table>
                          </div>
                      </div>
                    </div><?php } ?>
                    <div class="panel panel-default" >
                      <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#user_fc">
                        <h4 class="panel-title">Providers/Users</h4>
                      </div>
                      <div id="user_fc" class="panel-collapse collapse">
                          <div class="panel-body">
                               <table><div id='loading2' style='display:none; '></div>
                                    <tr>
                                        <td> <?php 
                                                $cnts=0; $ful=0;
                                                $user=sqlStatement("select * from users where username!='' and (fname!='' or lname !='')  order by username");
                                                while($urow2=sqlFetchArray($user)){
                                                   
                                                  $fac1=sqlStatement("select drive_sync_folder from tbl_user_custom_attr_1to1 where userid='".$urow2['id']."'");
                                                  $urow2=sqlFetchArray($fac1);
                                                  if($urow2['drive_sync_folder']==''|| empty($urow2)){
                                                    $cnts++;
                                                  }else {
                                                      $ful++;
                                                  }
                                                }
                                                echo "Count of Users need to create folder:<div id='cnt2'>".$cnts."</div>";
                                               if($cnts!=0) { ?>
                                            <a href="#" class="btn btn-primary" onclick="folder_creation('user_folder_creation.php','2','create');">Run</a><br>
                                                <?php } elseif ($cnts==0) { ?>
                                                    <a href="#" class="btn btn-primary" onclick="folder_creation('0','1','create');">Run</a> 
                                                <?php } 
//                                                $selection1 = sqlStatement("select * from tbl_drivesync_authentication where email='" .$email."' and folder_updation_status!=1 order by id desc");
//                                                $sel_rows1 = sqlFetchArray($selection1);
//                                                if($sel_rows1['status']=='updated'){
//                                                echo "Count of Users need to update folder:<div id='ful2'>".$ful."</div>";
                                                
                                                ?>
<!--                                                    <input type="button"  class="btn btn-primary" name="folder_update" id="folder_update"  value="update" onclick="folder_creation('user_folder_creation.php','2','change');"/>-->
                                                        <?php //} ?>
<!--                                               
<!--                                                From:<input type="text" name="from-limit2" id="from-limit2" value="0">-->
<!--                                            Limit:<input type="number" min='0' max='<?php //echo $cnt1; ?>'name="to-limit3" id="to-limit3" value=""><br>-->
                                            
                                            </td>
                                     </tr> 
                                </table>
                          </div>
                      </div>
                    </div>
                    <?php  if(in_array('insurance',$avail3)) { ?><div class="panel panel-default" >
                      <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#ins_fc">
                        <h4 class="panel-title">Insurance</h4>
                      </div>
                      <div id="ins_fc" class="panel-collapse collapse">
                          <div class="panel-body"><div id='loading3' style='display:none; '></div>
                               <table>
                                    <tr>
                                        <td>    
                                            <?php 
                                                $cnt=0; 
                                                $ins=sqlStatement("select * from insurance_companies order by name");
                                                while($urow1=sqlFetchArray($ins)){
                                                  $fac1=sqlStatement("select payer_folder,insuranceid from tbl_inscomp_custom_attr_1to1 where insuranceid='".$urow1['id']."'");
                                                  $urow2=sqlFetchArray($fac1);
                                                  if($urow2['payer_folder']==''|| empty($urow2)){
                                                    $cnt++;
                                                  }
                                                }
                                                
                                                    echo "Count of insurance companies need to create folder:<div id='cnt3'>".$cnt."</div>";  
                                                   
                                                ?>
<!--                                                From:<input type="text" name="from-limit6" id="from-limit6" value="0">-->
<!--                                            Limit:<input type="number" min='0' max='<?php echo $cnt; ?>'namysql_num_rows($ins);me="to-limit6" id="to-limit6" value=""><br>-->
                                                 <?php if($cnt!=0) { ?><a href="#" class="btn btn-primary " onclick="folder_creation('insurance_folder_creation.php','3','create');">Run</a><?php }elseif($cnt==0) {  ?>
                                                 <a href="#" id='insr' class="btn btn-primary " onclick="folder_creation('0','1','create');">Run</a><?php } ?>
                                                  
                                        </td>
                                     </tr> 
                                </table>
                          </div>
                      </div>

                    </div> <?php } ?>
                    <?php if(in_array('pharmacy',$avail3)) { ?><div class="panel panel-default" >
                      <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#pharmacy_fc">
                        <h4 class="panel-title">Pharmacy</h4>
                      </div>
                      <div id="pharmacy_fc" class="panel-collapse collapse"><div id='loading4' style='display:none; '></div>
                          <div class="panel-body">
                               <table>
                                    <tr>
                                        <td> 
                                            <?php 
                                                $cnt=0; 
                                                $ph=sqlStatement("select * from pharmacies order by name ");
                                                while($urow2=sqlFetchArray($ph)){
                                                  $ph1=sqlStatement("select pharmacy_folder from tbl_pharmacy_custom_attributes_1to1 where pharmacyid='".$urow2['id']."'");
                                                  $urow2=sqlFetchArray($ph1);
                                                  if($urow2['pharmacy_folder']==''|| empty($urow2)){
                                                    $cnt++;
                                                  }
                                                }
                                                echo "Count of Pharmacies need to create folder:<div id='cnt4'>".$cnt."</div>";
                                                
                                            ?>
<!--                                                From:<input type="text" name="from-limit4" id="from-limit4" value="0">-->
<!--                                            Limit:<input type="number" min='0' max='<?php echo $cnt1; ?>'name="to-limit4" id="to-limit4" value=""><br>-->
                                            <?php if($cnt!=0) { ?>
                                            <a href="#" id='phr' class="btn btn-primary" onclick="folder_creation('pharmacy_folder_creation.php','4','create');">Run</a>
                                            <?php } elseif ($cnt==0) { ?><a href="#" class="btn btn-primary" onclick="folder_creation('0','1','create');">Run</a> <?php } ?>
                                                
                                                
                                               
                                            </td>
                                     </tr> 
                                </table>
                          </div>
                      </div>
                    </div><?php }?>
                     <?php  if(in_array('addrbk',$avail3)) { ?>
                    <div class="panel panel-default" >
                      <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#addrbk_fc">
                        <h4 class="panel-title">Address Book</h4>
                      </div>
                      <div id="addrbk_fc" class="panel-collapse collapse"><div id='loading5' style='display:none; '></div>
                          <div class="panel-body">
                               <table>
                                    <tr>
                                        <td> 
                                            <?php 
                                                $cnt1=0; $num='';
                                                $selection = sqlStatement("select * from tbl_drivesync_authentication where email='" .$email."' order by id desc");
                                                $sel_rows = sqlFetchArray($selection);
                                                if($sel_rows['org_folder_query']!='' && $sel_rows['org_folder_query']!='0'){
                                                $query=$sel_rows['org_folder_query'];
                                                $addr=sqlStatement("select option_id  from list_options where list_id='abook_type' order by `seq` asc ");
                                                while($addr_urow=sqlFetchArray($addr)){
                                                    $query1=$query." where abook_type='".$addr_urow['option_id']."' "; 
                                                    $user=sqlStatement($query1);
                                                    $i=0;
                                                    while($urow=sqlFetchArray($user)) {
                                                        $folder_name=str_replace(" ","_",$urow['organization']);
                                                        $sel_custom=sqlStatement("select addressbook_folder,addrbk_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$urow['id']."'");
                                                        $row12=sqlFetchArray($sel_custom);
                                                        if($row12['addressbook_folder']=='' && $i==0){
                                                          $num=++$i+$num;
                                                        }
                                                        if($row12['addrbk_folder']==''){
                                                          $cnt1++;
                                                        }
                                                    
                                                    }
                                                }
                                               // echo "Count of Address book type need to create folder:<div id='cnt5'>".$num."</div>";
                                                 echo "Count of organisation need to create folder:<div id='cnt5'>".$cnt1."</div>";
                                                 
                                                }else {
                                                    echo "please enter Address book folder Format"; 
                                                }
                                                
                                            ?>
<!--                                                From:<input type="text" name="from-limit4" id="from-limit4" value="0">-->
<!--                                            Limit:<input type="number" min='0' max='<?php echo $cnt1; ?>'name="to-limit4" id="to-limit4" value=""><br>-->
                                            <?php if($cnt1!=0) { ?>
                                            <a href="#" id='addr' class="btn btn-primary" onclick="folder_creation('addrbk_folder_creation.php','5','create');">Run</a>
                                            <?php } elseif ($cnt1==0) { ?><a href="#" class="btn btn-primary" onclick="folder_creation('0','1','create');" >Run</a> <?php } ?>
                                                
                                            </td>
                                     </tr> 
                                </table>
                          </div>
                      </div>
                    </div><?php } ?>
                    <div class="panel panel-default" >
                      <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#facility_fc">
                        <h4 class="panel-title">Facility</h4>
                      </div>
                      <div id="facility_fc" class="panel-collapse collapse">
                          <div class="panel-body">
                               <table><div id='loading6' style='display:none; '></div>
                                    <tr>
                                        <td>
                                            <?php 
                                                $cnt6=0; 
                                                $fac=sqlStatement("select * from facility order by name");
                                                while($urow1=sqlFetchArray($fac)){
                                                  $fac1=sqlStatement("select facilityfolder,facilityid from tbl_facility_custom_attr_1to1 where facilityid='".$urow1['id']."'");
                                                  $urow2=sqlFetchArray($fac1);
                                                  if($urow2['facilityfolder']==''|| empty($urow2)){
                                                    $cnt6++;
                                                  }
                                                }
                                                echo "Count of Facilites need to create folder:<div id='cnt6'>".$cnt6."</div>";
                                                
                                            ?>
<!--                                                From:<input type="text" name="from-limit2" id="from-limit2" value="0">-->
<!--                                            Limit:<input type="number" min='0' max='<?php echo $cnt1; ?>'name="to-limit2" id="to-limit2" value=""><br>-->
                                            <?php if($cnt6!=0) { ?>
                                            <a href="#" id='fac2' class="btn btn-primary" onclick="folder_creation('facility_folder_creation.php','6');">Run</a>
                                            <?php } elseif ($cnt6==0) { ?><a href="#" class="btn btn-primary" onclick="folder_creation('0','1','create');" >Run</a> <?php } ?>
                                         </td>
                                     </tr> 
                                </table>
                          </div>
                      </div>
                    </div>
                    <div class="panel panel-default" >
                      <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#attach_create">
                        <h4 class="panel-title">Email Attachment</h4>
                      </div>
                      <div id="attach_create" class="panel-collapse collapse">
                          <div class="panel-body">
                               <table><div id='attach' style='display:none; '></div>
                                    <tr>
                                        <td>To Store Email Attachments to the Drive(<?php echo $email; ?>)
                                            <?php 
                                             $sel_imap=sqlStatement("select * from tbl_drivesync_authentication where email='".$email."'");
                                             $sel_row2=sqlFetchArray($sel_imap);
                                            $curl = curl_init();
                                            $form_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/storeEmailAttachmentcount/'.$sel_row2['imap_email'].'/pwd'; 
                                            curl_setopt($curl,CURLOPT_URL, $form_url);
                                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                                            $result_imap = curl_exec($curl);
                                            curl_close($curl);
                                            $email_uid = json_decode($result_imap, TRUE);
                                           
                                            echo "<br>Count of Emails:<div id='cntmail'>".count($email_uid)."</div>"; ?><br><a href="#" id='phr' class="btn btn-primary" onclick="store_email('<?php echo $result_imap; ?>');">Run</a></td>
                                     </tr> 
                                </table>
                          </div>
                      </div>
                    </div>
                 </div>
          </div>
             <div id="messages" class="tab-pane fade"> 
              <h3>Emr messages</h3>
              <div class="panel-group" id="accordion" style="padding:20px;">
              <div class="panel panel-default" >
                  <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#patient_msg"><div id='patients1' style='display:none; '></div>
                    <h4 class="panel-title">Patients</h4>
                  </div>
                  <div id="patient_msg" class="panel-collapse collapse">
                      <div class="panel-body">
                          <table>
                              <tr><td>Count of messages need to create: <?php echo "<div id='patients_1'>".  message_count($email,'patients'); ?></td></tr> 
                             <tr><td><a href="#" class="btn btn-primary" onclick="msg_creation('create_emr_msg.php','patients','1');">Run</a></td></tr>
                           </table>
                      </div>
                  </div>
                </div>
              <div class="panel panel-default" >
                  <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#pro_msg"><div id='users2' style='display:none; '></div>
                    <h4 class="panel-title">Providers/Users</h4>
                  </div>
                  <div id="pro_msg" class="panel-collapse collapse">
                      <div class="panel-body">
                          <table>
                             <tr><td>Count of messages need to create: <?php echo "<div id='users_2'>".  message_count($email,'users')."</div>"; ?></td></tr>
                             <tr><td> <a href="#" class="btn btn-primary" onclick="msg_creation('create_emr_msg.php','users','2');">Run</a></td></tr>
                           </table>
                      </div>
                  </div>
                </div>
              <div class="panel panel-default" >
                  <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#ins_msg"><div id='insurance3' style='display:none; '></div>
                    <h4 class="panel-title">insurance</h4>
                  </div>
                  <div id="ins_msg" class="panel-collapse collapse">
                      <div class="panel-body">
                          <table>
                              <tr>Count of messages need to create: <?php echo "<div id='insurance_3'>".  message_count($email,'insurance')."</div>"; ?></tr>
                             <tr> <a href="#" class="btn btn-primary" onclick="msg_creation('create_emr_msg.php','insurance','3');">Run</a></tr>
                           </table>
                      </div>
                  </div>
                </div>
              <div class="panel panel-default" >
                  <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#ph_msg"><div id='pharmacy4' style='display:none; '></div>
                    <h4 class="panel-title">Pharmacy</h4>
                  </div>
                  <div id="ph_msg" class="panel-collapse collapse">
                      <div class="panel-body">
                          <table>
                              <tr>Count of messages need to create: <?php echo "<div id='pharmacy_4'>".  message_count($email,'pharmacy')."</div>"; ?></tr>
                             <tr> <a href="#" class="btn btn-primary" onclick="msg_creation('create_emr_msg.php','pharmacy','4');">Run</a></tr>
                           </table>
                      </div>
                  </div>
                </div>
              <div class="panel panel-default" >
                  <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#addrbk_msg"><div id='address_Book5' style='display:none; '></div>
                    <h4 class="panel-title">Address Book</h4>
                  </div>
                  <div id="addrbk_msg" class="panel-collapse collapse">
                      <div class="panel-body">
                          <table>
                              <tr><td>Count of messages need to create: <?php echo "<div id='address_Book_5'>". message_count($email,'address_Book')."</div>"; ?></td></tr>
                             <tr><td><a href="#" class="btn btn-primary" onclick="msg_creation('create_emr_msg.php','address_Book','5');">Run</a></td></tr>
                           </table>
                      </div>
                  </div>
                </div>
               <div class="panel panel-default" >
                  <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#facility_msg"><div id='facility6' style='display:none; '></div>
                    <h4 class="panel-title">Facility</h4>
                  </div>
                  <div id="facility_msg" class="panel-collapse collapse">
                      <div class="panel-body">
                          <table>
                              <tr><td>Count of messages need to create: <?php echo "<div id='facility_6'>". message_count($email,'facility')."</div>"; ?></td></tr>
                              <tr> <td><a href="#" class="btn btn-primary" onclick="msg_creation('create_emr_msg.php','facility','6');">Run</a></td></tr>
                           </table>
                      </div>
                  </div>
                </div>
              </div>
             </div>  
          
    </div>
    <!-- Modal for tree-->
        <div class="modal fade" name = "linked" id="linkedfolder" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                             <h4 class="modal-title" id="myModalLabel"></h4>
                        </div>
                        <div class="modal-body">
                            <div class="modalcloader" style="text-align: center">Data Loading...</div>
                            <div id="deletemessage">
                                
                            </div>
                            
                        </div>
                    </div>
                </div>
        </div>
<!--<script type='text/javascript' src='../js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='../js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='../js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='../js/dataTables.colVis.js'></script>-->
<!--<script type='text/javascript' src='../js/bootstrap-3.0.3.min.js'></script>-->
<script type='text/javascript'>
$(document).ready( function () {
    $('.aclink').click(function(evt){
        evt.preventDefault();
        var slide = $(this).data("contenar");
        $(this).find(".sign > i").toggleClass("glyphicon-plus");
        $(this).find(".sign > i").toggleClass("glyphicon-minus");
        $("#"+slide).slideToggle();
    })
    function ajaxcall(url,data,type,callback,errorcallback,asynctype){
        var async = asynctype || true;
        currentajaxcall = $.ajax({url:url,async:async,type:type,xhrFields: {withCredentials: true},data:data, crossDomain: true,error:errorcallback,success:callback});
    }
        
    $('#patient_data').DataTable( {
        dom: 'T<"clear">lfrtip',
        "tableTools": {
            "sSwfPath": "../../swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sButtonText": "Save to Excel"
                }
            ]
        }
    } );
    $('#user').DataTable( {
    } );
    
    $('#linkedfolder').on("show.bs.modal",function(event){
        var categorydata = [{}];
        var target = $(event.relatedTarget);
        var modal = $(this);
        var items = JSON.parse('<?php echo json_encode($category_arr) ?>');
        modal.find('#deletemessage').html("");
        $('.modalcloader').show();
        modal.find(".modal-title").html(target.data("username")+" Linked Folders");
        modal.find('#deletemessage').html('<ul id="tree"></ul>');
        $.each(items,function(index,value){
            ajaxcall('<?php echo $link_url ?>',{category:value['title'],userid:target.data('userid')},"post",function(data){linkeddata(data,value['title'])},function(){},true);
        });       
       var count=[];
       var empty = 0;
        function linkeddata(data,elm){
          categorydata[0][elm] = $.map(JSON.parse(data),function(key,value){
              return key;
          });
          if(categorydata[0][elm].length==0){
              empty++;
              if(empty == items.length){
                $('.modalcloader').hide();
                $('#tree:empty').html("No folders link this user").css({paddingLeft:0});
             }
          }
          
           $.each(categorydata[0][elm],function(index,value){
               count.push("success");
               ajaxcall('<?php echo $foulderinfo ?>'+"/"+value,null,"get",function(data1){
                   getfiledata(data1,count,categorydata,index,elm,value);                 
               },function(){
                   errorcalback(count,categorydata,index,elm)
               },true);
           });
        }
        
        function getfiledata(data1,count,categorydata,index,elm,value){
            count.pop();
            if(data1 !=" "){
                var data = JSON.parse(data1);
                if(data[0] != undefined){
                    if(data[0].error_code == "403"){
                        count.push("success");
                        setTimeout(ajaxcall('<?php echo $foulderinfo ?>'+"/"+value,null,"get",function(data1){
                                getfiledata(data1,count,categorydata,index,elm,value);                 
                            },function(){
                                errorcalback(count,categorydata,index,elm)
                            },true)
                        ,1000);
                        return;
                    }
                }
                var foldername = data.name;
                categorydata[0][elm][index] = foldername;
                console.log(count.length)
            }else{
                delete categorydata[0][elm][index]
            }
            if(count.length === 0){
                 listview(categorydata)
            }
        }
        
        function errorcalback(count,categorydata,index,elm){
             count.pop(); categorydata[0][elm][index] = null;
            if(count.length === 0){
                 listview(categorydata)
            }
        }
        
        function listview(categorydata){
            $('.modalcloader').hide();
            removenotfounditems(categorydata);
            $.each(categorydata[0],function(index,value){
                var html = value.length !=0?"<li> <a href='#'>"+index.replace("_"," ").capitalizeFirstLetter()+"</a><ul>":"";
                    $.each(value,function(i,v){
                        html += "<li>"+v+"</li>";
                    });
                html +=value.length !=0?"</ul></li>":"";
                modal.find('#deletemessage #tree').append(html);
            });
            $('#tree:empty').html("No folders link this user")
            $('#tree').treed({openedClass : 'glyphicon-folder-open', closedClass : 'glyphicon-folder-close'});
        }
        
        function removenotfounditems(categorydata){
            $.each(categorydata[0],function(index,value){
               categorydata[0][index] = $.map(value,function(i,v){
                    if(i != null)
                    {
                        return i;
                    }
                });
            })
        }
      
      
        
    });
    
    $('#driveusers a').click(function(evt){
          evt.preventDefault();
          if(!$(this).hasClass('active')){
            $('#driveusers a').removeClass('active');
            $(this).addClass("active");
            $.ajax({
                url:"user_folder_view.php",
                type: 'POST',
                data: {type:$(this).data("type")},
                success: function (data, textStatus, jqXHR) {
                    var users = JSON.parse(data)
                    var usershtml = "";
                    $.each(users,function(id,username){
                       usershtml += "<div class='uyd-user'>\n\
                                        <div class='uyd-avatar'>\n\
                                            <div class='name' title='"+username+"'>\n\
                                                <img src='../../../images/user-img2.png'  width='40' height='40' border='0' >\n\
                                                <div class='username'>"+username+"\
                                                </div>\n\
                                            </div>\n\
                                            <div class='linkbtn'>\n\
                                                <a class='various-big btn btn-primary' data-url='link_folders.php?userid="+id+"&username="+username+"' href='#'>Link To Folders</a>\n\
                                                <a data-toggle='modal' title='Show linkend folders list' data-username='"+username+"' data-userid='"+id+"' data-target='#linkedfolder' href='javascript:void(0)' style='padding:6px;'>\n\
                                                    <img src='images/linkfoulder.png' width='35'/>\n\
                                                </a>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>";
                    });
                    $('#userscontenar > div').html(usershtml);
                    $(".various-big").showDrieFrame();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                        
                }
            })
          }
      });
      $('#driveusers a').eq(0).trigger('click')
} );
String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}
 </script>
</body>
</html>