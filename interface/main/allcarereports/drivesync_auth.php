<?php
 require_once("../../globals.php");
 require_once("objects_function.php");
 $email=$_REQUEST['email'];
 if($_REQUEST['status']==1){
   
     echo $email. " Sucessfully Authenticated<br/>";
     
 }
 $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://'; 
 $category_arr= array(
        array('title' =>'patient','fieldid'=>'parent_folder'),
        array('title' =>'users','fieldid'=>'user_parent_folder'),
        array('title' =>'insurance','fieldid'=>'ins_parent_folder'),
        array('title' =>'pharmacy','fieldid'=>'pharmacy_parent_folder'),
        array('title' =>'agency','fieldid'=>'addrbk_parent_folder'),
        array('title' =>'facility','fieldid'=>'facility_parent_folder'),
        array('title' =>'scan','fieldid'=>'scan_parent_folder')
);
$link_url =$protocol.$_SERVER['HTTP_HOST']."/interface/patient_file/summary/link_folders_save.php";
$foulderinfo = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/getfileinfo_web/' . $email; 
$addfolder_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$email."/";
?>
<!DOCTYPE html>
<html>  
    <head>
	<meta charset='UTF-8'>
        <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
	<title>Configuration Page</title>
        <link rel='stylesheet' type='text/css' href='../css/bootstrap-3.0.3.min.css'>
        <link rel='stylesheet' type='text/css' href='../css/jquery.dataTables.css'>
        <link rel='stylesheet' type='text/css' href='../css/dataTables.tableTools.css'>
        <link rel='stylesheet' type='text/css' href='../css/dataTables.colVis.css'>
        <link rel='stylesheet' type='text/css' href='../css/dataTables.colReorder.css'>
        <link rel='stylesheet' type='text/css' href='css/driveframe.css'>
        <script type='text/javascript' src='../js/jquery-1.11.1.min.js'></script>
        <link rel="stylesheet" href="../../reports/fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
        <script type="text/javascript" src="../../reports/fancybox/source/jquery.fancybox.pack.js"></script>
        <script type='text/javascript' src='js/driveframe.js'></script>
	<style>
        
        .tab-pane label {
          /*background: #eee; */
          padding: 5px; 
          /*border: 1px solid #ccc; */
          margin-left: -1px; 
          position: relative;
          left: 1px; 
        }
        .tab-pane [type=radio] {
          display: none;   
        }
        
        [type=radio]:checked ~ label {
          background: white;
          border-bottom: 1px solid white;
          z-index: 2;
        }
        [type=radio]:checked ~ label ~ .content {
          z-index: 1;
        }
        td {
            padding:10px;
        }
        #submit {
            margin: 0 auto 0 50%;
        }
        .inline{
            width: 45%;
            float: left;
        }
        .uyd-user {
            float: left;
            margin: 0 20px 20px 0;
            border-radius: 5px;
            box-shadow: 0px 0px 4px #bbb;
            padding: 10px;
            background: #DFDFDF;
            width: 185px;
            height: 115px;
            position: relative;
        }
        .uyd-avatar {
            float: left;
        }
        .button {
            background-color: #428bca; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            -webkit-transition-duration: 0.4s; /* Safari */
            transition-duration: 0.4s;
        }

        .button1 {
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
        }

        .button1:hover {
            box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19);
        }
        #loading, #loading1,#loading6,#loading2,#loading3,#loading5,#loading4,#attach{
            background: transparent url('../../pic/ajax-loader-large.gif') no-repeat 0 0;
            font-size: 0px;
            display: inline-block;
            width: 125px;
            height: 118px;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
        }
        #loading, #loading1,#loading6,#loading2,#loading3,#loading5,#loading4,#attach{
            background: transparent url('../../pic/ajax-loader-large.gif') no-repeat 0 0;
            font-size: 0px;
            display: inline-block;
            width: 125px;
            height: 118px;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
        }
         #patients1, #users2,#insurance3,#pharmacy4,#address_Book5,#facility6{
            background: transparent url('../../pic/ajax-loader-large.gif') no-repeat 0 0;
            font-size: 0px;
            display: inline-block;
            width: 125px;
            height: 118px;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
        }
        .linkbtn{
            position: absolute;
            bottom: 5px;
        }
        .name img{
            float: left;
        }
        .username{
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 89px;
            padding: 8px 0px 0px 6px;
            float: left;
        }
        .viewlinks {
            position: relative;
            top: 20px;
        }
        .modal-dialog {
            margin: 30px auto;
            width: 426px;
        }
        #driveusers{
            margin: 10px 0px;
        }
        #userscontenar{
            left: 0;
            margin: 0 auto;
            width: 90%;
            padding-top:20px;
        }
        .aclink{
            display:block;
            border: 1px solid #51c3eb;
            text-transform: uppercase;
            text-decoration: none;
            line-height: 20px;
        }
        .aclink:hover,.aclink:focus,.aclink:active{
            text-decoration: none;
            outline: none;
        }
        .aclink .sign {
            background-color: #51c3eb;
            margin-left: 0;
            color: #fff;
            display: inline-block;
            padding: 9px 24px 9px 11px;
            width: 14px;
        }
        .aclink .lbl {
            color: #51c3eb;
            font-size: 12px;
            display: inline-block;
            padding: 8px 0 0 8px;
            color: black;
            
        }
        #ac1contenar,#ac2contenar{
            display: none;
            padding: 10px;
            border: 1px solid #51c3eb;
            margin-top: -1px;
        }
        #ac2{
            margin-top: 10px;
        }
        /* enable absolute positioning */
        .left-inner-addon {
            position: relative;
        }
        .left-inner-addon input {
            padding-left: 30px;    
        }
        .left-inner-addon i {
            position: absolute;
            padding: 10px 12px;
            pointer-events: none;
        }
        #loglist ul li{
            list-style-type: none;
        }
	</style>
        <script>
            $(function(){
                
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
                $('#loglist').DataTable( {
                    "ajax": "log_data.php?email="+'<?php echo $email; ?>'+"&user="+'<?php echo $_SESSION['authUser']; ?>',
                    "columns": [
                        { "data": "email" },
                        { "data": "date" },
                        { "data": "user" },
                        { "data": "patient_id" },
                        { "data": "google_folder" },
                        { "data": "file_name" },
                        { "data": "status" },
                        { "data": "watsID" },
                        { "data": "category" }
                    ]
                } );
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

                         alert('Please Fill Agency folder link');
                        errorno++;
                         return false;
                    }else if($('#facility-parent-folder').val()==''){
                         alert('Please Fill Facility  folder link');
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
                        alert("please use checkbox option only for Agency..");   
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
                        //console.log(data);
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
        $selection=sqlStatement("select * from tbl_drivesync_authentication where email='$email' order by id desc");
        $sel_rows=sqlFetchArray($selection);
        function getdata($title,$email,$protocol){
           // echo $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$email.'/root/'.$title;
            $curl = curl_init(); 
            curl_setopt($curl,CURLOPT_URL,$protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$email.'/root/'.$title);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
            $result = curl_exec($curl);
            $resultant = explode(":",$result);
            curl_close($curl);
            return 'https://drive.google.com/drive/folders/'.$resultant[0];
        }
    ?>
    <div class="container">
        <h2>Allcare Document Management Configuration</h2>
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#history">History</a></li>
          <li><a data-toggle="tab" href="#settings">Settings</a></li>
          <li><a data-toggle="tab" href="#users">Users</a></li>
          <li><a data-toggle="tab" href="#folder_crt">Folder Creation</a></li>
        </ul>
        <div class="tab-content">
            <div id="settings" class="tab-pane fade ">
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
                                            <div class="form-group">
                                                <div class="row">
                                                   <div class="col-sm-3">
                                                       Parent Folder Link:
                                                   </div>
                                                   <div class="col-sm-6">
                                                        <?php
                                                         if( $sel_rows['practice_parent_folder']==''){
                                                             $title=sqlStatement("select * from globals where gl_name='openemr_name'");
                                                             $t=sqlfetchArray($title);
                                                             $practice=getdata(str_replace(" ","_",$t['gl_value']),$email,$protocol); 
                                                         }else {
                                                             $practice= $sel_rows['practice_parent_folder'];
                                                         }
                                                        ?>
                                                       <input type="url" name="instance-parent-folder" class="form-control" id="instance-parent-folder" value="<?php echo $practice; ?>" readonly=""/>
                                                   </div>
<!--                                                    <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn" value="Create Folder" data-toggle="modal" data-target="#addfoulder" data-isroot="true" data-soursefield="instance-parent-folder">Create Folder</button>
                                                    </div>-->
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                               </div> 
                               <div class="panel panel-default" >
                                    <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#patients">
                                        <h4 class="panel-title">Patient</h4>
                                    </div>
                                    <div id="patients" class="panel-collapse collapse">
                                        <div class="panel-body"> 
                                            <div class="form-group">
                                               <div class="row"> 
                                                   <div class="col-sm-3">
                                                       Patient Parent Folder Link:
                                                   </div>
                                                   <div class="col-sm-6">
                                                       <input type="url" name="parent-folder" class="form-control" id="parent-folder" value="<?php echo $sel_rows['parent_folder']; ?>" readonly=""/>
                                                   </div>
                                                   <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn" data-soursefield="parent-folder" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="disabled">Create Folder</button>
                                                    </div>
                                               </div>  
                                            </div> 
                                        <div class="form-group">
                                           <?php
                                                 $sel_attr=explode("_",$sel_rows['patient_folder_format']);
                                           ?>
                                            <div class="row"> 
                                               <div class="col-sm-3">
                                                   Patient Folder Name Format:
                                               </div>
                                               <div class="col-sm-3">
                                                   <input type="text" name="patient-folder" id="patient-folder" class="form-control" value="<?php echo $sel_rows['patient_folder_format']; ?>"  />
                                               </div>
                                                <div class="col-sm-3">
                                                    (Select combination of this attributes to generate patient folder name format:)
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="checkbox" class="patient"  id="pfname"    value="fname"  <?php if(in_array('fname',$sel_attr)) { echo "checked"; }?>/><label for="pfname">Patient First Name</label><br/>
                                                    <input type="checkbox" class="patient"  id="plname"    value="lname"  <?php if(in_array('lname',$sel_attr)) { echo "checked"; }?> /><label for="lname">Patient Last Name</label><br/>
                                                    <input type="checkbox" class="patient"  id="pid"       value="pid"    <?php if(in_array('pid',$sel_attr))   { echo "checked"; }?> /><label for="pid">Patient ID</label><br/>
                                                    <input type="checkbox" class="patient"  id="DOB"       value="DOB"    <?php if(in_array('DOB',$sel_attr))   { echo "checked"; }?> /><label for="DOB">Date of Birth</label><br/>
                                                </div>
                                           </div>
                                       </div>
                                            <div class="form-group">
                                           <?php
                                                 $sel_attr1=explode("_",$sel_rows['patient_file_format']);
                                           ?>
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    Patient File Name Format:
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="text" name="patient-file" class="form-control" id="patient-file" value="<?php echo $sel_rows['patient_file_format']; ?>" />
                                                </div>
                                                <div class="col-sm-3">
                                                    (Select combination of this attributes to generate patient file name format:)
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="checkbox" class="patient-file"  id="fpfname" value="fname"   <?php if(in_array('fname',$sel_attr1)) { echo "checked"; }?>/><label for="fpfname">Patient First Name</label><br/>
                                                    <input type="checkbox" class="patient-file"  id="fplname" value="lname"   <?php if(in_array('lname',$sel_attr1)) { echo "checked"; }?> /><label for="fplname">Patient Last Name</label><br/>
                                                    <input type="checkbox" class="patient-file"  id="fpid"    value="pid"     <?php if(in_array('pid',$sel_attr1))   { echo "checked"; }?> /><label for="fpid">Patient ID</label><br/>
                                                    <input type="checkbox" class="patient-file"  id="fDOB"    value="DOB"     <?php if(in_array('DOB',$sel_attr1))   { echo "checked"; }?> /><label for="fDOB">Date of Birth</label><br/>
                                                    <input type="checkbox" class="patient-file"  id="dos"    value="dos"     <?php if(in_array('dos',$sel_attr1))   { echo "checked"; }?> /><label for="dos">Date Of Service</label><br/>
                                                </div>
                                            </div>    
                                       </div>
                                            <div class="form-group">
                                           <div class="row">
                                               <div class="col-sm-6">
                                                   <input type="checkbox"   id="auto-trigger"   name="auto-trigger" value="<?php echo $sel_rows['patient_folder_trigger'];?>"     <?php if($sel_rows['patient_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('auto-trigger');"/><label for="auto-trigger">Patient Folder Creation (while new patient created)</label><br/>
                                               </div>
                                               <div class="col-sm-6"> 
                                                   <input type="checkbox"   id="psubfolder"   name="psubfolder" value="<?php echo $sel_rows['patient_sub_folder'];?>"     <?php if($sel_rows['patient_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('psubfolder');"/><label for="psubfolder">Patient SubFolder Creation</label> 
                                               </div>
                                           </div>
                                       </div>
                                        </div>
                                    </div>
                               </div>
                               <div class="panel panel-default" >
                                   <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#users-settings">
                                       <h4 class="panel-title">User</h4>
                                   </div>
                                   <div id="users-settings" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                               <div class="row">
                                                   <div class="col-sm-3">
                                                       User Parent Folder Link:
                                                   </div>
                                                   <div class="col-sm-6">
                                                       <input type="url" name="user-parent-folder" class="form-control" id="user-parent-folder" value="<?php echo $sel_rows['user_parent_folder']; ?>" readonly=""/>
                                                   </div>
                                                   <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn" data-soursefield="user-parent-folder" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                    </div>
                                               </div>
                                            </div> 
                                            <div class="form-group">
                                                   <?php 
                                                         $sel_attr2=explode("_",$sel_rows['provider_folder_format']);
                                                   ?>
                                                   <div class="row">
                                                       <div class="col-sm-3">
                                                           User Folder Name Format:
                                                       </div>
                                                       <div class="col-sm-3">
                                                           <input type="text" name="provider-folder" class="form-control" id="provider-folder" value="<?php echo $sel_rows['provider_folder_format']; ?>" />
                                                       </div>
                                                       <div class="col-sm-3">
                                                        (Select combination of this attributes to generate provider folder name format:)
                                                       </div>
                                                        <div class="col-sm-3">
                                                            <input type="checkbox" class="provider"  id="username"     value="username" <?php if(in_array('username',$sel_attr2)) { echo "checked"; }?> /><label for="username">User name</label><br/>
                                                            <input type="checkbox" class="provider"  id="ufname"       value="fname"    <?php if(in_array('fname',$sel_attr2))    { echo "checked"; }?>/><label for="ufname">Provider First Name</label><br/>
                                                            <input type="checkbox" class="provider"  id="ulname"       value="lname"    <?php if(in_array('lname',$sel_attr2))    { echo "checked"; }?> /><label for="ulname">Provider Last Name</label><br/>
                                                            <input type="checkbox" class="provider"  id="npi"          value="npi"      <?php if(in_array('npi',$sel_attr2))      { echo "checked"; }?> /><label for="npi">NPI</label><br/>
                                                        </div>
                                                    </div>    
                                               </div>
                                          <div class="form-group">
                                               <div class="row">
                                                   <div class="col-sm-6">
                                                       <input type="checkbox"   id="user-auto-trigger"   name="user-auto-trigger" value="<?php echo $sel_rows['provider_folder_trigger']; ?>"     <?php if($sel_rows['provider_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('user-auto-trigger');"/><label for="user-auto-trigger">User Folder Creation (while new patient created) </label><br/>
                                                   </div>
                                                   <div class="col-sm-6">
                                                       <input type="checkbox"   id="provider-sub-folder"   name="provider-sub-folder" value="<?php echo $sel_rows['provider_sub_folder'];?>"     <?php if($sel_rows['provider_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('provider-sub-folder');"/><label for="provider-sub-folder">User SubFolder Creation</label><br/>
                                                   </div>
                                               </div>    
                                           </div>
                                        </div>
                                   </div>
                               </div>
                               <div class="panel panel-default" >
                                 <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#insurance">
                                   <h4 class="panel-title">Insurance</h4>
                                 </div>
                                 <div id="insurance" class="panel-collapse collapse">
                                     <div class="panel-body">
                                            <div class="form-group">
                                                   <div class="row">
                                                       <div class="col-sm-3">
                                                           Insurance Parent Folder Link:
                                                       </div>
                                                       <div class="col-sm-6">
                                                           <input type="url" name="ins-parent-folder" class="form-control" id="ins-parent-folder" value="<?php echo $sel_rows['ins_parent_folder']; ?>" readonly=""/>
                                                       </div>
                                                       <div class="col-sm-3">
                                                            <button class="btn btn-primary createfolderbtn" data-soursefield="ins-parent-folder" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                        </div>
                                                   </div>
                                               </div> 
                                               <div class="form-group">
                                                   <?php 
                                                         $sel_attr3=explode("_",$sel_rows['insurance_folder_format']);
                                                   ?>
                                                   <div class="row">
                                                       <div class="col-sm-3">
                                                           Insurance Folder Name Format:
                                                       </div>
                                                       <div class="col-sm-3">
                                                           <input type="text" name="insurance-folder" class="form-control" id="insurance-folder" value="<?php echo $sel_rows['insurance_folder_format']; ?>" />
                                                       </div>
                                                        <div class="col-sm-3">
                                                        (Select combination of this attributes to generate insurance folder name format:)
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <input type="checkbox" class="insurance"  id="ins-name"      value="name" <?php if(in_array('name',$sel_attr3)) { echo "checked"; }?> /><label for="ins-name">name</label>
                                                         </div>
                                                    </div>   
                                               </div>
                                               <div class="form-group">
                                                   <div class="col-sm-6">
                                                       <input type="checkbox"   id="ins-auto-trigger"   name="ins-auto-trigger" value="<?php echo $sel_rows['ins_folder_trigger'];?>"     <?php if($sel_rows['ins_folder_trigger']=='yes')   { echo "checked"; }?>  onclick="check_status('ins-auto-trigger');"/><label for="ins-auto-trigger">Insurance Folder Creation (while new Insurance Company created)</label><br/>
                                                   </div>
                                                   <div class="col-sm-6">
                                                       <input type="checkbox"   id="insurance-sub-folder"   name="insurance-sub-folder" value="<?php echo $sel_rows['insurance_sub_folder'];?>"     <?php if($sel_rows['insurance_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('insurance-sub-folder');"/><label for="insurance-sub-folder">Insurance SubFolder Creation</label> <br/>
                                                   </div>
                                               </div>
                                         </div>
                                    </div> 
                               </div>
                                <div class="panel panel-default">
                                     <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#pharmacy">
                                       <h4 class="panel-title">Pharmacy</h4>
                                     </div>
                                     <div id="pharmacy" class="panel-collapse collapse">
                                         <div class="panel-body">
                                            <div class="form-group">
                                               <div class="row">
                                                   <div class="col-sm-3">
                                                       Pharmacy Parent Folder Link:
                                                   </div>
                                                   <div class="col-sm-6">
                                                       <input type="url" name="pharmacy-parent-folder" class="form-control" id="pharmacy-parent-folder" value="<?php echo $sel_rows['pharmacy_parent_folder']; ?>" readonly=""/>
                                                   </div>
                                                   <div class="col-sm-3">
                                                       <button class="btn btn-primary createfolderbtn" data-soursefield="pharmacy-parent-folder" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                    </div>
                                               </div>
                                           </div> 
                                            <div class="form-group">
                                               <?php 
                                                     $sel_attr4=explode("_",$sel_rows['pharmacy_folder_format']);
                                               ?>
                                               <div class="row">
                                                   <div class="col-sm-3">
                                                       Pharmacy Folder Name Format:
                                                   </div>
                                                   <div class="col-sm-3">
                                                       <input type="text" name="pharmacy-folder" id="pharmacy-folder"  class="form-control" value="<?php echo $sel_rows['pharmacy_folder_format']; ?>"/>
                                                   </div>
                                                    <div class="col-sm-3">
                                                        (Select combination of this attributes to generate pharmacy folder name format:)
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="checkbox" class="pharmacy"  id="ph-name"      value="name" <?php if(in_array('name',$sel_attr4)) { echo "checked"; }?> /><label for="ph-name">name</label><br/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                               <div class="row">
                                                   <div class="col-sm-6">
                                                       <input type="checkbox"   id="ph-auto-trigger"   name="ph-auto-trigger" value="<?php echo $sel_rows['pharmacy_folder_trigger']; ?>"     <?php if($sel_rows['pharmacy_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('ph-auto-trigger');"/><label for="ph-auto-trigger">Pharmacy Folder Creation (while new Pharmacy  created)</label> <br/>
                                                   </div>
                                                   <div class="col-sm-6">
                                                       <input type="checkbox"   id="pharmacy-sub-folder"   name="pharmacy-sub-folder" value="<?php echo $sel_rows['pharmacy_sub_folder'];?>"     <?php if($sel_rows['pharmacy_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('pharmacy-sub-folder');"/><label for="pharmacy-sub-folder">Pharmacy SubFolder Creation</label>  <br/>
                                                   </div>
                                               </div>    
                                            </div>
                                         </div>
                                     </div>
                               </div>
                                <div class="panel panel-default" >
                                     <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#address_book">
                                       <h4 class="panel-title">Agency</h4>
                                     </div>
                                    <div id="address_book" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                               <div class="row">
                                                   <div class="col-sm-3">
                                                       Agency Parent Folder Link:
                                                   </div>
                                                   <div class="col-sm-6">
                                                       <input type="url" name="addrbk-parent-folder" class="form-control" id="addrbk-parent-folder" value="<?php echo $sel_rows['addrbk_parent_folder']; ?>"  readonly=""/>
                                                   </div>
                                                   <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn" data-soursefield="addrbk-parent-folder" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                    </div>
                                               </div>
                                            </div> 
                                            <div class="form-group">
                                                <?php 
                                                     $sel_attr6=explode("_",$sel_rows['org_folder_format']);
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        Organisation Folder Name Format:
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" name="org-folder" class="form-control" id="org-folder" value="<?php echo $sel_rows['org_folder_format']; ?>" />
                                                    </div>
                                                    <div class="col-sm-3">
                                                    (Select combination of this attributes to generate agency folder name format:)
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="checkbox" class="org"  id="org-name"      value="organization" <?php if(in_array('organization',$sel_attr6)) { echo "checked"; }?> /><label for="org-name">Agency Type</label><br/>
                                                    </div>
                                                </div>    
                                            </div>
                                               <div class="form-group">
                                                   <div class="row">
                                                       <div class="col-sm-6">
                                                           <input type="checkbox"   id="addr-auto-trigger"   name="addr-auto-trigger" value="<?php echo $sel_rows['addrbk_folder_trigger'];?>"     <?php if($sel_rows['addrbk_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('addr-auto-trigger');"/><label for="addr-auto-trigger">Agency Folder Creation (while new Organisation  created)</label><br/>
                                                       </div>
                                                       <div class="col-sm-6">
                                                           <input type="checkbox"   id="addrbk-sub-folder"   name="addrbk-sub-folder" value="<?php echo $sel_rows['addrbk_sub_folder']; ?>"     <?php if($sel_rows['addrbk_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('addrbk-sub-folder');"/><label for="addrbk-sub-folder">Agency SubFolder Creation  </label><br/>
                                                       </div>
                                                   </div>   
                                               </div>
                                        </div>
                                    </div>
                               </div>
                                <div class="panel panel-default" >
                                    <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#facility">
                                        <h4 class="panel-title">Facility</h4>
                                    </div>
                                    <div id="facility" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        Facility Parent Folder Link:
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <input type="url" name="facility-parent-folder" class="form-control" id="facility-parent-folder" value="<?php echo $sel_rows['facility_parent_folder']; ?>" readonly=""/>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn" data-soursefield="facility-parent-folder" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <?php 
                                                     $sel_attr7=explode("_",$sel_rows['facility_folder_format']);
                                                ?>
                                                <div class="row">
                                                   <div class="col-sm-3">
                                                       Facility Folder Name Format:
                                                   </div>
                                                   <div class="col-sm-3">
                                                       <input type="text" name="facility-folder" class="form-control" id="facility-folder" value="<?php echo $sel_rows['facility_folder_format']; ?>"/>
                                                   </div>
                                                    <div class="col-sm-3">
                                                        (Select combination of this attributes to generate facility folder name format:)
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="checkbox" class="facility"  id="fac-name"  class="form-control"    value="name" <?php if(in_array('name',$sel_attr7)) { echo "checked"; }?> /><label for="fac-name">name</label><br/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <input type="checkbox"   id="facility-folder-trigger"   name="facility-folder-trigger" value="<?php echo $sel_rows['facility_folder_trigger'];?>"     <?php if($sel_rows['facility_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('facility-folder-trigger');"/><label for="facility-folder-trigger">Facility Creation (while new Organisation  created)</label><br/>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <input type="checkbox"   id="facility-sub-folder"   name="facility-sub-folder" value="<?php echo $sel_rows['facility_sub_folder']?>"     <?php if($sel_rows['facility_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('facility-sub-folder');"/><label for="facility-sub-folder">Facility  SubFolder Creation </label><br/>
                                                    </div>
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default" >
                                    <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#scan">
                                        <h4 class="panel-title">Scan</h4>
                                    </div>
                                    <div id="scan" class="panel-collapse collapse">
                                         <div class="panel-body"> 
                                            <div class="form-group">
                                               <div class="row"> 
                                                   <div class="col-sm-3">
                                                       Scan Parent Folder Link:
                                                   </div>
                                                   <div class="col-sm-6">
                                                       <input type="url" name="scan-parent-folder" class="form-control" id="scan-parent-folder" value="<?php echo $sel_rows['scan_parent_folder']; ?>" readonly=""/>
                                                   </div>
                                                   <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn  scan-parent" data-soursefield="scan-parent-folder" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                    </div>
                                               </div>
                                            </div> 
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        Scan DFW MH TPHC Med Record Folder Link: 
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <input type="text" name="scan-medical-charts"  class="form-control" id="scan-medical-charts" value="<?php echo $sel_rows['scan_medical_charts']; ?>" readonly=""/>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn  scan" data-soursefield="scan-medical-charts" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                    </div>
                                                </div>
                                            </div>
                                             <!-- Scan Medical Cpo Folder Link-->
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        Scan DFW MH TPHC Payers Folder Link: 
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <input type="text" name="scan-medical-payer" class="form-control" id="scan-medical-payer" value="<?php echo $sel_rows['scan_medical_payer']; ?>" readonly=""/>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn  scan" data-soursefield="scan-medical-payer" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                    </div>
                                                </div>
                                            </div>
                                             <!--Scan Medical Accounts Link -->
                                             <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        Scan MDPA Payments Folder Link: 
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <input type="text" name="scan-medical-payment" class="form-control" id="scan-medical-payment" value="<?php echo $sel_rows['scan_medical_payment']; ?>" readonly=""/>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn  scan" data-soursefield="scan-medical-payment" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        Scan Medical DFW Medical Home TPHC Billing Folder Link: 
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <input type="text" name="scan-medical-mhtb" class="form-control" id="scan-medical-mhtb" value="<?php echo $sel_rows['scan_medical_mhtb']; ?>" readonly=""/>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn  scan" data-soursefield="scan-medical-mhtb" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        Scan DFW MH Correspondence Folder Link: 
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <input type="text" name="scan-medical-mhc" class="form-control" id="scan-medical-mhc" value="<?php echo $sel_rows['scan_medical_mhc']; ?>" readonly=""/>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button class="btn btn-primary createfolderbtn  scan" data-soursefield="scan-medical-mhc" data-isroot="false" value="Create Folder" data-toggle="modal" data-target="#addfoulder" disabled="">Create Folder</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                               </div>
<!--                                <div class="panel panel-default" >
                                    <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#attachment">
                                        <h4 class="panel-title">Email Attachment</h4>
                                    </div>
                                    <div id="attachment" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                           <input type="text" name="imap-pwd"  class="form-control" id="imap-pwd" value="" style="width:190px;"/>
                                                    </div>
                                                </div>
                                            </div> 
                                                                                       
                                        </div>
                                    </div>
                               </div>-->
                         </div>
                         
                    </div> 
                    
                     <input type="hidden" name="email"  id="email"  value="<?php echo $email; ?>" />
                     <input type="submit" id="save" name="save" value="Save" class="btn btn-primary" />
                     </form> 
            </div>
            <div id="history" class="tab-pane fade in active">
                <table id='loglist' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Date</th>
                            <th>User</th>
                            <th>ID</th>
                            <th>Google FolderLink</th>
                            <th>File Name</th>
                            <th>Status</th>
                            <th>WatsID</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                 </table>
            </div>
            <div id="users" class="tab-pane fade"> 
                <div id="driveusers">
                  <div class="btn-group btn-group-justified">
                    <a href="#" data-type="emr" class="btn btn-primary">Emr Users</a>
                    <a href="#" data-type="patients" class="btn btn-primary">Patient Users</a>
                    <a href="#" data-type="agencies" class="btn btn-primary">Agency Users</a>
                  </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3">
                            <div class="inner-addon left-inner-addon">
                                <i class="glyphicon glyphicon-search"></i>
                                 <input type="text" class="form-control"  id="userSearch" placeholder="Search" >
                            </div>
                        </div>
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
                        <h4 class="panel-title">Patient</h4>
                      </div>
                      <div id="patient_fc" class="panel-collapse collapse"><div id='loading1' style='display:none; '></div>
                          <div class="panel-body">
                               <table>
                                    <tr>
                                        <td> 
                                            <?php 
                                                $cnt=0; $pat=0;
                                                //$ph=sqlStatement("select * from patient_data");
                                                //while($urow2=sqlFetchArray($ph)){
                                                  $ph1=sqlStatement("select patient_folder from patient_data where  patient_folder='' ");
                                                  while($urow2=sqlFetchArray($ph1)){
                                                      $cnt++;
                                                  }
                                                  
                                                  
                                                //}
                                                echo "Count of Patients need to create folder:<div id='cnt1'>".$cnt."</div>";
                                                //if($cnt!=0) { ?>
<!--                                                    <a href="#" class="btn btn-primary" onclick="folder_creation('patient_folder_creation.php','1','create');">Run</a> <br>-->
                                                <?php //} elseif ($cnt==0) { ?>
<!--                                                    <a href="#" class="btn btn-primary" onclick="folder_creation('0','1','create');">Run</a> -->
                                                <?php //} 
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
                        <h4 class="panel-title">User</h4>
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
                                               //if($cnts!=0) { ?>
<!--                                                <a href="#" class="btn btn-primary" onclick="folder_creation('user_folder_creation.php','2','create');">Run</a><br>-->
                                                <?php //} elseif ($cnts==0) { ?>
<!--                                                    <a href="#" class="btn btn-primary" onclick="folder_creation('0','1','create');">Run</a> -->
                                                <?php //} 
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
                                                 <?php // if($cnt!=0) { ?>
<!--                                            <a href="#" class="btn btn-primary " onclick="folder_creation('insurance_folder_creation.php','3','create');">Run</a>-->
                                                <?php //}elseif($cnt==0) {  ?>
<!--                                                 <a href="#" id='insr' class="btn btn-primary " onclick="folder_creation('0','1','create');">Run</a>-->
                                                     <?php //} ?>
                                                  
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
                                            <?php //if($cnt!=0) { ?>
<!--                                            <a href="#" id='phr' class="btn btn-primary" onclick="folder_creation('pharmacy_folder_creation.php','4','create');">Run</a>-->
                                            <?php //} elseif ($cnt==0) { ?>
<!--                                            <a href="#" class="btn btn-primary" onclick="folder_creation('0','1','create');">Run</a> -->
                                                <?php //} ?>
                                                
                                                
                                               
                                            </td>
                                     </tr> 
                                </table>
                          </div>
                      </div>
                    </div><?php }?>
                     <?php  if(in_array('addrbk',$avail3)) { ?>
                    <div class="panel panel-default" >
                      <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"  data-target="#addrbk_fc">
                        <h4 class="panel-title">Agency</h4>
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
                                            <?php //if($cnt1!=0) { ?>
<!--                                            <a href="#" id='addr' class="btn btn-primary" onclick="folder_creation('addrbk_folder_creation.php','5','create');">Run</a>-->
                                            <?php //} elseif ($cnt1==0) { ?>
<!--                                                <a href="#" class="btn btn-primary" onclick="folder_creation('0','1','create');" >Run</a> -->
                                            <?php //} ?>
                                                
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
                                            <?php //if($cnt6!=0) { ?>
<!--                                            <a href="#" id='fac2' class="btn btn-primary" onclick="folder_creation('facility_folder_creation.php','6');">Run</a>-->
                                            <?php //} elseif ($cnt6==0) { ?>
<!--                                            <a href="#" class="btn btn-primary" onclick="folder_creation('0','1','create');" >Run</a> -->
                                            <?php //} ?>
                                         </td>
                                     </tr> 
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
     <!-- Modal for Add New-->
        <div class="modal fade" name = "addfoulder" id="addfoulder" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="vertical-alignment-helper">
                <div class="modal-dialog vertical-align-center modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                             <h4 class="modal-title" id="myModalLabel">Create Folder</h4>
                        </div>
                        <div class="modal-body">
                            <div><input type="text" id="newfoldername"  class="form-control input-sm" value="New folder"/></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="addfoldrbtn" data-loading-text="Processing..." data-defaultpart="https://drive.google.com/drive/folders/">Create Folder</button>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
<script type='text/javascript' src='../js/bootstrap-3.0.3.min.js'></script>
<script type='text/javascript' src='../js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='../js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='../js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='../js/dataTables.colVis.js'></script>

<script type='text/javascript'>
 var rootfolderid ='';
 var scanroot="";
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
                    document.getElementById('userSearch').value="";
                    $('#userscontenar > div').html(usershtml);
                    $(".various-big").showDrieFrame();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                        
                }
            })
          }
      });
      $('#driveusers a').eq(0).trigger('click')
      $("#userSearch").on("keyup",function(){
        var inputval = $(this).val().trim();
        $('#userscontenar .uyd-user').hide();
        $('#userscontenar .uyd-user').filter(function(){
            var patt = new RegExp(inputval,"i");
            var res = patt.test($(this).find('.username').text());
            return res;
        }).show();

    })
        if($("#instance-parent-folder").val().trim() != ""){
            var getfolderval = $("#instance-parent-folder").val().split("/");
            rootfolderid = getfolderval[getfolderval.length-1];
            $("#parent-folder").val().trim()==""?$(".createfolderbtn").prop("disabled",false):$(".createfolderbtn").prop("disabled",true)
        }
        if($("#instance-parent-folder").val().trim()!="" && $("#scan-parent-folder").val().trim() == ""){
            $(".scan-parent").prop("disabled",false);
            $(".scan").prop("disabled",true);
        }else if($("#scan-parent-folder").val().trim() != ""){
            var getscanroot = $("#scan-parent-folder").val().split("/");
            scanroot = getscanroot[getscanroot.length-1];
            $.each($('.scan'),function(index,value){
               if($(this).parent().prev().find("input").val().trim() == "")
                   $(this).prop("disabled",false);
            });
        }
        
//        if($("#instance-parent-folder").val().trim()!="" && $("#email-parent-folder").val().trim() == "")
//            $(".mailattach").prop("disabled",false);
        
        $('#addfoulder').on("show.bs.modal",function(event){
            var target = $(event.relatedTarget);
            var modal = $(this);
            modal.find("#addfoldrbtn").data("sourseid",target.data("soursefield"));
            if(target.data("isroot") == true){
               modal.find("#addfoldrbtn").data("rootid","root");
           }else if(target.hasClass("scan")){
               modal.find("#addfoldrbtn").data("rootid",scanroot); 
           }else
               modal.find("#addfoldrbtn").data("rootid",rootfolderid); 
            
            if(target.hasClass("scan-parent"))
                modal.data("createtype","scan")
            else
                modal.data("createtype","rootbase")
            
            $('#newfoldername').val("New folder");
            
         });
    
        $('#addfoldrbtn').click(function(evt){
            var $self = $(this);
            var url =  '<?php echo $addfolder_url ?>'+$self.data("rootid")+"/"+$('#newfoldername').val();
            $self.button('loading');
            $.ajax({url:url,type:"get",success: function (data, textStatus, jqXHR) {
                    $self.button('reset');
                    if($self.data("rootid") == "root"){
                        rootfolderid = data.split(":")[0];
                        $("#"+$self.data("sourseid")).val($self.data("defaultpart")+rootfolderid);
                        $(".createfolderbtn").first().prop("disabled",true);
                        $(".createfolderbtn").not($(".createfolderbtn").first()).prop("disabled",false);
                    }else{
                        if($('#addfoulder').data("createtype") == "scan"){
                            $('.scan').prop("disabled",false)
                            scanroot = data.split(":")[0];
                        }
                        $("#"+$self.data("sourseid")).val($self.data("defaultpart")+data.split(":")[0]);
                        $("#"+$self.data("sourseid")).parent().next().find(".createfolderbtn").prop("disabled",true);
                    }
                    
                    $('#addfoulder').modal("hide");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("folder createing Error" + textStatus)
                }
            });
        });
} );
String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}
 </script>
</body>
</html>