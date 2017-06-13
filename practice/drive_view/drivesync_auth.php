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
include '../section_header.php';
$pagename = "configuration"; 

 $sql=sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
 $row=sqlFetchArray($sql);
 $email=$row['notes']; 
 
 $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://'; 
 $category_arr= array(
        array('title' =>'patients','fieldid'=>'parent_folder'),
        array('title' =>'users','fieldid'=>'user_parent_folder'),
        array('title' =>'insurance','fieldid'=>'ins_parent_folder'),
        array('title' =>'pharmacy','fieldid'=>'pharmacy_parent_folder'),
        array('title' =>'agency','fieldid'=>'addrbk_parent_folder'),
        array('title' =>'facility','fieldid'=>'facility_parent_folder'),
        array('title' =>'scan','fieldid'=>'scan_parent_folder')
);
 
$selection = sqlStatement("select * from tbl_drivesync_authentication where email='" .$email . "'");
$sel_rows = sqlFetchArray($selection);
 
$sel_attr=explode("_",$sel_rows['patient_folder_format']);
$sel_attr1=explode("_",$sel_rows['patient_file_format']);
$sel_attr2=explode("_",$sel_rows['provider_folder_format']);
$sel_attr3=explode("_",$sel_rows['insurance_folder_format']);
$sel_attr4=explode("_",$sel_rows['pharmacy_folder_format']);
$sel_attr6=explode("_",$sel_rows['org_folder_format']);
$sel_attr7=explode("_",$sel_rows['facility_folder_format']);

$link_url = "//".$_SERVER['HTTP_HOST']."/practice/drive_view/link_folders_save.php";
$foulderinfo = '//' . $_SERVER['HTTP_HOST'] . '/api/DriveSync/getfileinfo_web/' . $email; 

function message_count($mail,$category){ 
         $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://'; 
         $category_arr= array(
                array('title' =>'patients','fieldid'=>'parent_folder'),
                array('title' =>'users','fieldid'=>'user_parent_folder'),
                array('title' =>'insurance','fieldid'=>'ins_parent_folder'),
                array('title' =>'pharmacy','fieldid'=>'pharmacy_parent_folder'),
                array('title' =>'agency','fieldid'=>'addrbk_parent_folder'),
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
?>
<link type="text/css" rel="stylesheet" href="driveassets/css/responsive_tab/responsive-tabs.css" />
<link type="text/css" rel="stylesheet" href="driveassets/css/responsive_tab/smk-accordion.css" />
<!-- datatable -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.0/css/responsive.bootstrap.min.css"/>
<link rel='stylesheet' type='text/css' href='driveassets/css/driveframe.css'>
<!--end-->
<script src="driveassets/js/responsive_tab/jquery.responsiveTabs.js"></script>
<script src="driveassets/js/responsive_tab/smk-accordion.js"></script>
<script type='text/javascript' src='driveassets/js/driveframe.js'></script>
<!--datatable -->
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.0/js/responsive.bootstrap.min.js"></script>
</head> 
<style>
 @media only screen
and (min-device-width : 320px)
and (max-device-width : 1024px)
and (orientation : portrait) {
.rowodd{background-color:gainsboro;}
.roweven{background-color:#D3D3EE;}
}
 .tab-pane label {
  background: #eee; 
  padding: 10px; 
  border: 1px solid #ccc; 
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
    background: transparent url('../drive_view/driveassets/driveimages/ajax-loader-large.gif') no-repeat 0 0;
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
    background: transparent url('../drive_view/driveassets/driveimages/ajax-loader-large.gif') no-repeat 0 0;
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
    background: transparent url('../drive_view/driveassets/driveimages/ajax-loader-large.gif') no-repeat 0 0;
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
/*label {
    display: inline-block;
    width: 8em;
    text-align: right;
    padding: 3px;
}*/
input[type=text], input[type=url] {
    float:right;
}
ul .tree {
    display:block;
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


</style>
    <script>
        $(document).ready(function(){
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
                        { "data": "category" }
                    ]
                } );
        
              $.ajax({
                 type: "POST",
                 url:"authentication_check.php",
                 data : {email:'<?php echo $email; ?>'},
                 success: function(data) {
                     
                     if(data=='sucess'){
                         document.getElementById('settings').style.display='block';
                         document.getElementById('horizontalTab').style.display='block';
                     
                    }else if(data=='fail'){
                        auth_checkid();
                    }
                         
                 },
                 error: function(jqXHR, exception){
                    alert("failed" + jqXHR.responseText);
                 } 
            });
            
            var p1= [], p2= [],p3= [],p4= [],p5= [], p6= [],p7= [], p8=[];

                    //patient
                    $.each($(".patient:checked"),function(index,value){
                        p1.push($(value).val())
                    })
                    var $target = $('#patient-folder');
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

                    //user
                    $.each($(".provider:checked"),function(index,value){
                        p2.push($(value).val())
                    })
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

                    //patient file            
                    $.each($(".patient-file:checked"),function(index,value){
                        p3.push($(value).val())
                    })

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

                 //insurance
                $.each($(".insurance:checked"),function(index,value){
                    p4.push($(value).val())
                })
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

                //pharmacy                
                $.each($(".pharmacy:checked"),function(index,value){
                    p5.push($(value).val())
                })
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

                //facility
                $.each($(".facility:checked"),function(index,value){
                    p6.push($(value).val())
                })
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

                //organisation
                $.each($(".org:checked"),function(index,value){
                    p8.push($(value).val())
                })

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

                         alert('Please Fill Agency folder link');
                        errorno++;
                         return false;
                    }else if($('#facility-parent-folder').val()==''){
                         alert('Please Fill Facility  folder link');
                         errorno++;
                         return false;
                    }
//                    else if($('#email_parent_folder').val()==''){
//                         alert('Please Fill Email Attachment  folder link');
//                         errorno++;
//                         return false;
//                    }
//                    else if($('#imap-user').val()==''){
//                         alert('Please Fill imap email');
//                         errorno++;
//                         return false;
//                    }
//                    else if($('#imap-pwd').val()==''){
//                         alert('Please Fill Email password');
//                         errorno++;
//                         return false;
//                    }
//                    if($('#imap-user').val()!='<?php echo $email; ?>'){
//                         alert('Please Fill configured email only');
//                         errorno++;
//                         return false;
//                    }
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
                        alert("please use checkbox option only for Agency.");   
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
                
                function ajaxcall(url,data,type,callback,errorcallback,asynctype){
                    var async = asynctype || true;
                    currentajaxcall = $.ajax({url:url,async:async,type:type,xhrFields: {withCredentials: true},data:data, crossDomain: true,error:errorcallback,success:callback});
                }
                
                
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
                            url:"folder_creation/user_folder_view.php",
                            type: 'POST',
                            data: {type:$(this).data("type")},
                                success: function (data, textStatus, jqXHR) {
                                    var users = JSON.parse(data)
                                    var usershtml = "";
                                    $.each(users,function(id,username){
                                       usershtml += "<div class='uyd-user'>\n\
                                                        <div class='uyd-avatar'>\n\
                                                            <div class='name' title='"+username+"'>\n\
                                                                <img src='../drive_view/driveassets/driveimages/user-img2.png'  width='40' height='40' border='0' >\n\
                                                                <div class='username'>"+username+"\
                                                                </div>\n\
                                                            </div>\n\
                                                            <div class='linkbtn'>\n\
                                                                <a class='various-big btn btn-primary' data-url='link_folders.php?userid="+id+"&username="+username+"' href='#'>Link To Folders</a>\n\
                                                                <a data-toggle='modal' title='Show linkend folders list' data-username='"+username+"' data-userid='"+id+"' data-target='#linkedfolder' href='javascript:void(0)' style='padding:6px;'>\n\
                                                                    <img src='../drive_view/driveassets/driveimages/linkfoulder.png' width='35'/>\n\
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
                
               $(document).on("click","#auth",function(event){
                  event.preventDefault();
                   var url=$(this).data('href');
                   window.open(url,"_blank","width=500,height=500");
               })
               
                $("#userSearch").on("keyup",function(){
                    var inputval = $(this).val().trim();
                    $('#userscontenar .uyd-user').hide();
                    $('#userscontenar .uyd-user').filter(function(){
                        var patt = new RegExp(inputval,"i");
                        var res = patt.test($(this).find('.username').text());
                        return res;
                    }).show();
                    
                })
        });
        
        String.prototype.capitalizeFirstLetter = function() {
            return this.charAt(0).toUpperCase() + this.slice(1);
        }
        
        
        
        
        function auth_checkid(){
           
             $.ajax({
                 type: "POST",
                 url:"drivesync_config.php",
                 data : {email:'<?php echo $email; ?>'},
                 success: function(data) {
                    if(data=='sucess'){
                         document.getElementById('settings').style.display='block';
                         document.getElementById('horizontalTab').style.display='block';
                         
                     }else if(data=='fail')
                         alert('fail');
                     else{
                         document.getElementById('settings').style.display='none';
                         $('#auth_check').html(data);
                         
                     }
                 },
                 error: function(jqXHR, exception){
                    alert("failed" + jqXHR.responseText);
                 } 
            });
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
        
       
    </script>
    <div id="auth_check"></div>
    <div id="settings" style="display:none;">
       <?php echo $email; ?> successfully Authenticated
    </div>

  
     <div id="horizontalTab" style="border-bottom:0px; display:none;">
        <ul>
            <li><a href="#tab-1">Settings</a></li>
            <li><a href="#tab-2">History</a></li>
            <li><a href="#tab-3">Users</a></li>
            <li><a href="#tab-4">Folder Creation</a></li>
            <li><a href="#tab-5">Messages</a></li>
        </ul>
        
        <div id="tab-1">
        <form name="driveset" id="driveset" method="post" action="" > 
            <div class="accordion_close">

                <!-- Practice -->
                <div class="accordion_in">
                    <div class="acc_head">Practice</div>
                        <div class="acc_content">
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
                                    <b>Parent Folder Link:</b><input type="url" name="instance-parent-folder" id="instance-parent-folder" value="<?php echo $sel_rows['practice_parent_folder']; ?>" />
                                </div>
                            </div>
                            </div>
                        </div>
                </div>

                <!-- Patients-->
                <div class="accordion_in  acc_active">
                    <div class="acc_head">Patient</div>
                        <div class="acc_content">
                            <div class="container-fluid">
                                <div class="row rowodd" style="padding-bottom:5px;">
                                    <div class="col-sm-9 col-lg-4">
                                        <b>Patients Parent Folder Link:</b><input type="url" name="parent-folder"  id="parent-folder" value="<?php echo $sel_rows['parent_folder']; ?>" />
                                    </div>
                                </div>
                                <div class="row roweven" >
                                    <div class="col-sm-5 col-lg-4">
                                        <b>Patient Folder Name Format:</b><input type="text" name="patient-folder" id="patient-folder" value="<?php echo $sel_rows['patient_folder_format']; ?>" />
                                    </div>
                                    <div class="col-sm-3 col-lg-4">
                                        (Select combination of this attributes to generate patient folder name format:)
                                    </div>
                                    <div class="col-sm-4 col-lg-4">
                                        <input type="checkbox" class="patient"  id="pfname"    value="fname"  <?php if(in_array('fname',$sel_attr)) { echo "checked"; }?> />Patient First Name<br/>
                                        <input type="checkbox" class="patient"  id="plname"    value="lname"  <?php if(in_array('lname',$sel_attr)) { echo "checked"; }?> />Patient Last Name<br/>
                                        <input type="checkbox" class="patient"  id="pid"       value="pid"    <?php if(in_array('pid',$sel_attr))   { echo "checked"; }?> />Patient ID<br/>
                                        <input type="checkbox" class="patient"  id="DOB"       value="DOB"    <?php if(in_array('DOB',$sel_attr))   { echo "checked"; }?> />Date of Birth<br/>
                                    </div>
                                </div>
                                <div class="row rowodd" >
                                    <div class="col-sm-5 col-lg-4">
                                        <b>Patient File Name Format:</b><input type="text" name="patient-file" id="patient-file" value="<?php echo $sel_rows['patient_file_format']; ?>" />
                                    </div>
                                    <div class="col-sm-3 col-lg-4">
                                        (Select combination of this attributes to generate patient file name format:)
                                    </div>
                                    <div class="col-sm-4 col-lg-4">
                                        <input type="checkbox" class="patient-file"  id="pfname" value="fname"   <?php if(in_array('fname',$sel_attr1)) { echo "checked"; }?>/>Patient First Name<br/>
                                        <input type="checkbox" class="patient-file"  id="plname" value="lname"   <?php if(in_array('lname',$sel_attr1)) { echo "checked"; }?> />Patient Last Name<br/>
                                        <input type="checkbox" class="patient-file"  id="pid"    value="pid"     <?php if(in_array('pid',$sel_attr1))   { echo "checked"; }?> />Patient ID<br/>
                                        <input type="checkbox" class="patient-file"  id="DOB"    value="DOB"     <?php if(in_array('DOB',$sel_attr1))   { echo "checked"; }?> />Date of Birth<br/>
                                        <input type="checkbox" class="patient-file"  id="dos"    value="dos"     <?php if(in_array('dos',$sel_attr1))   { echo "checked"; }?> />Date Of Service<br/>
                                    </div>
                                </div>
                                <div class="row roweven" >
                                    <div class="col-sm-6 col-lg-3">
                                        <input type="checkbox"   id="auto-trigger" name="auto-trigger"  value="<?php echo $sel_rows['patient_folder_trigger'];?>"     <?php if($sel_rows['patient_folder_trigger']=='yes'){ echo "checked"; }?> onclick="check_status('auto-trigger');"/>Patient Folder Creation (while new patient created) <br/>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">    <input type="checkbox"   id="psubfolder"   name="psubfolder"    value="<?php echo $sel_rows['patient_sub_folder'];?>"     <?php if($sel_rows['patient_sub_folder']=='yes')    { echo "checked"; }?> onclick="check_status('psubfolder');"/>Patient SubFolder Creation <br/>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

                <!-- Users -->
                <div class="accordion_in">
                    <div class="acc_head">User</div>
                        <div class="acc_content">
                                <div class="container-fluid">
                                    <div class="row rowodd" >
                                        <div class="col-sm-9 col-lg-4" style="padding-bottom:5px;">
                                            <b>Users Parent Folder Link:</b><input type="url" name="user-parent-folder" id="user-parent-folder" value="<?php echo $sel_rows['user_parent_folder']; ?>"/>
                                        </div>
                                    </div>
                                    <div class="row roweven" >
                                        <div class="col-sm-5 col-lg-4">
                                            <b>Users Folder Name Format:</b><input type="text" name="provider-folder" id="provider-folder" value="<?php echo $sel_rows['provider_folder_format']; ?>" />
                                         </div>
                                        <div class="col-sm-3 col-lg-4">
                                            (Select combination of this attributes to generate user folder name format:)
                                        </div>
                                        <div class="col-sm-4 col-lg-4">
                                            <input type="checkbox" class="provider"  id="username"     value="username" <?php if(in_array('username',$sel_attr2)) { echo "checked"; }?> />User name<br/>
                                            <input type="checkbox" class="provider"  id="pfname"       value="fname"    <?php if(in_array('fname',$sel_attr2))    { echo "checked"; }?>/>Provider First Name<br/>
                                            <input type="checkbox" class="provider"  id="plname"       value="lname"    <?php if(in_array('lname',$sel_attr2))    { echo "checked"; }?> />Provider Last Name<br/>
                                            <input type="checkbox" class="provider"  id="pid"          value="npi"      <?php if(in_array('npi',$sel_attr2))      { echo "checked"; }?> />NPI<br/>
                                        </div>
                                    </div>
                                    <div class="row rowodd" >
                                        <div class="col-sm-6 col-lg-3">
                                            <input type="checkbox"   id="user-auto-trigger"   name="user-auto-trigger" value="<?php echo $sel_rows['provider_folder_trigger'];?>"     <?php if($sel_rows['provider_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('user-auto-trigger');"/>User Folder Creation (while new patient created) <br/>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <input type="checkbox"   id="provider-sub-folder"   name="provider-sub-folder" value="<?php echo $sel_rows['provider_sub_folder']; ?>"     <?php if($sel_rows['provider_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('provider-sub-folder');"/>User SubFolder Creation  <br/>
                                        </div>
                                    </div>
                                </div>
                        </div>
                </div>
                
                <!-- Insurance -->
                <div class="accordion_in">
                    <div class="acc_head">Insurance</div>
                        <div class="acc_content">
                            <div class="container-fluid">
                                <div class="row rowodd" >
                                    <div class="col-sm-9 col-lg-4" style="padding-bottom:5px;">
                                        <b>Insurance Parent Folder Link:</b><input type="url" name="ins-parent-folder" id="ins-parent-folder" value="<?php echo $sel_rows['ins_parent_folder']; ?>" />
                                    </div>
                                </div>
                                <div class="row roweven" >
                                    <div class="col-sm-5 col-lg-4">
                                        <b>Insurance Folder Name Format:</b><input type="text" name="insurance-folder" id="insurance-folder" value="<?php echo $sel_rows['insurance_folder_format']; ?>" />
                                     </div>
                                    <div class="col-sm-3 col-lg-4">
                                        (Select combination of this attributes to generate insurance folder name format:)
                                    </div>
                                    <div class="col-sm-4 col-lg-4">
                                        <input type="checkbox" class="insurance"  id="ins-name"      value="name" <?php if(in_array('name',$sel_attr3)) { echo "checked"; }?> />name<br/>
                                    </div>
                                </div>
                                <div class="row rowodd" >
                                    <div class="col-sm-6 col-lg-3">
                                        <input type="checkbox"   id="ins-auto-trigger"   name="ins-auto-trigger" value="<?php echo $sel_rows['ins_folder_trigger'];?>"     <?php if($sel_rows['ins_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('ins-auto-trigger');"/>Insurance Folder Creation (while new Insurance Company created) <br/>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <input type="checkbox"   id="insurance-sub-folder"   name="insurance-sub-folder" value="<?php echo $sel_rows['insurance_sub_folder']; ?>"     <?php if($sel_rows['insurance_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('insurance-sub-folder');"/>Insurance SubFolder Creation  <br/>
                                    </div>
                                </div>
                            </div>
                        </div>
               </div>
                
                <!-- Pharmacy -->
                <div class="accordion_in">
                    <div class="acc_head">Pharmacy</div>
                        <div class="acc_content">
                            <div class="container-fluid">
                                <div class="row rowodd" >
                                    <div class="col-sm-9 col-lg-4" style="padding-bottom:5px;">
                                        <b>Pharmacy Parent Folder Link:</b><input type="url" name="pharmacy-parent-folder" id="pharmacy-parent-folder" value="<?php echo $sel_rows['pharmacy_parent_folder']; ?>" />
                                    </div>
                                </div>
                                <div class="row roweven" >
                                    <div class="col-sm-6 col-lg-4">
                                        <b>Pharmacy Folder Name Format:</b><input type="text" name="pharmacy-folder" id="pharmacy-folder" value="<?php echo $sel_rows['pharmacy_folder_format']; ?>" />
                                     </div>
                                    <div class="col-sm-3 col-lg-3">
                                        (Select combination of this attributes to generate pharmacy folder name format:)
                                    </div>
                                    <div class="col-sm-2 col-lg-4">
                                        <input type="checkbox" class="pharmacy"  id="ph-name"      value="name" <?php if(in_array('name',$sel_attr4)) { echo "checked"; }?> />name<br/>
                                    </div>
                                </div>
                                <div class="row rowodd" >
                                    <div class="col-sm-6 col-lg-3">
                                        <input type="checkbox"   id="ph-auto-trigger"   name="ph-auto-trigger" value="<?php echo $sel_rows['pharmacy_folder_trigger'];?>"     <?php if($sel_rows['pharmacy_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('ph-auto-trigger');"/>Pharmacy Folder Creation (while new Pharmacy  created) <br/>
                                    </div>
                                    <div class="col-sm-6 col-lg-3"> 
                                        <input type="checkbox"   id="pharmacy-sub-folder"   name="pharmacy-sub-folder" value="<?php echo $sel_rows['pharmacy_sub_folder']; ?>"     <?php if($sel_rows['pharmacy_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('pharmacy-sub-folder');"/>Pharmacy SubFolder Creation  <br/>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <!-- Address Book -->
                <div class="accordion_in">
                    <div class="acc_head">Agency</div>
                    <div class="acc_content">
                        <div class="container-fluid">
                            <div class="row rowodd" >
                                <div class="col-sm-9 col-lg-5" style="padding-bottom:5px;">
                                    <b>Agency  Parent Folder Link:</b><input type="url" name="addrbk-parent-folder" id="addrbk-parent-folder" value="<?php echo $sel_rows['addrbk_parent_folder']; ?>" />
                                </div>
                            </div>
                            <div class="row roweven" >
                                <div class="col-sm-5 col-lg-5">
                                    <b>Organisation Folder Name Format:</b><input type="text" name="org-folder" id="org-folder" value="<?php echo $sel_rows['org_folder_format']; ?>" />
                                 </div>
                                <div class="col-sm-3 col-lg-3">
                                    (Select combination of this attributes to generate Agency folder name format:)
                                </div>
                                <div class="col-sm-4 col-lg-4">
                                    <input type="checkbox" class="org"  id="org-name"      value="organization" <?php if(in_array('organization',$sel_attr6)) { echo "checked"; }?> />Agency Type<br/>
                                </div>
                            </div>
                            <div class="row rowodd" >
                                <div class="col-sm-6 col-lg-3">
                                    <input type="checkbox"   id="addr-auto-trigger"   name="addr-auto-trigger" value="<?php echo $sel_rows['addrbk_folder_trigger']; ?>"     <?php if($sel_rows['addrbk_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('addr-auto-trigger');"/>Agency Folder Creation (while new Organisation  created) <br/>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <input type="checkbox"   id="addrbk-sub-folder"   name="addrbk-sub-folder" value="<?php echo $sel_rows['addrbk_sub_folder'];?>"     <?php if($sel_rows['addrbk_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('addrbk-sub-folder');"/>Agency  SubFolder Creation  <br/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Facility -->
                <div class="accordion_in">
                    <div class="acc_head">Facility</div>
                    <div class="acc_content">
                        <div class="container-fluid">
                            <div class="row rowodd" >
                                <div class="col-sm-9 col-lg-4" style="padding-bottom:5px;">
                                    <b>Facility Parent Folder Link:</b><input type="url" name="facility-parent-folder" id="facility-parent-folder" value="<?php echo $sel_rows['facility_parent_folder']; ?>" />
                                </div>
                            </div>
                            <div class="row roweven" >
                                <div class="col-sm-5 col-lg-4">
                                    <b>Facility Folder Name Format:</b><input type="text" name="facility-folder" id="facility-folder" value="<?php echo $sel_rows['facility_folder_format']; ?>" />
                                 </div>
                                <div class="col-sm-3 col-lg-4">
                                    (Select combination of this attributes to generate facility folder name format:)
                                </div>
                                <div class="col-sm-4 col-lg-4">
                                    <input type="checkbox" class="facility"  id="fac-name"      value="name" <?php if(in_array('name',$sel_attr7)) { echo "checked"; }?> />name<br/>
                                </div>
                            </div>
                            <div class="row rowodd" >
                                <div class="col-sm-6 col-lg-3">
                                    <input type="checkbox"   id="facility-folder-trigger"   name="facility-folder-trigger" value="<?php echo $sel_rows['facility_folder_trigger'];?>"     <?php if($sel_rows['facility_folder_trigger']=='yes')   { echo "checked"; }?> onclick="check_status('facility-folder-trigger');"/>Facility Creation (while new Organisation  created) <br/>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <input type="checkbox"   id="facility-sub-folder"   name="facility-sub-folder" value="<?php echo $sel_rows['facility_sub_folder']; ?>"     <?php if($sel_rows['facility_sub_folder']=='yes')   { echo "checked"; }?> onclick="check_status('facility-sub-folder');"/>Facility  SubFolder Creation  <br/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- scan -->
                <div class="accordion_in">
                    <div class="acc_head">Scan</div>
                    <div class="acc_content">
                        <div class="container-fluid">
                            <div class="row rowodd" >
                                <div class="col-sm-9 col-lg-4" style="padding-bottom:5px;">
                                    <b>Scan Parent Folder Link:</b><input type="url" name="scan-parent-folder" id="scan-parent-folder" value="<?php echo $sel_rows['scan_parent_folder']; ?>"/>
                                </div>
                            </div>
                            <div class="row roweven" >
                                <div class="col-sm-9 col-lg-4">
                                    <b>Scan Medical Charts Folder Link:</b><input type="text" name="scan-medical-charts" id="scan-medical-charts" value="<?php echo $sel_rows['scan_medical_charts']; ?>" />
                                 </div>
                            </div>
                            <div class="row rowodd" >
                                <div class="col-sm-9 col-lg-4">
                                    <b>Scan Medical Cpo Folder Link:</b><input type="text" name="scan-medical-cpo" id="scan-medical-cpo" value="<?php echo $sel_rows['scan_medical_cpo']; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Email Attachment-->
                <div class="accordion_in">
                    <div class="acc_head">Email Attachment</div>
                    <div class="acc_content">
                        <div class="container-fluid">
                            <div class="row rowodd" >
                                <div class="col-sm-9 col-lg-4" style="padding-bottom:5px;">
                                    Parent Folder Link:<input type="url" name="email-parent-folder" id="email-parent-folder" value="<?php echo $sel_rows['email_parent_folder']; ?>" />
                                </div>
                            </div>
                            <div class="row roweven" >
                                <div class="col-sm-5 col-lg-4">
                                    <b>Username:</b><input type="text" name="imap-user" id="imap-user" value="<?php echo $sel_rows['imap_email']; ?>" />
                                 </div>
                                <div class="col-sm-3 col-lg-4">
                                    <b>Password:</b><input type="text" name="imap-pwd" id="imap-pwd" value="<?php echo str_replace("/","@",$sel_rows['imap_pwd']); ?>" />
                                </div>
                                <div class="col-sm-4 col-lg-4">
                                    <?php 
                                          $sql=sqlStatement("select * from tbl_user_custom_attr_1to1 where email='$email'"); 
                                          $userid=sqlFetchArray($sql); 
                                          if($userid['userid']==$_SESSION['authId']){  
                                              echo "<input type='checkbox' id='ch_pwd' name='ch_pwd' value='' onclick='change_pwd()' enabled/>Change Password"; 
                                          }else { 
                                              echo "<input type='checkbox' id='ch_pwd' name='ch_pwd' value='' onclick='change_pwd()' disabled/>Change Password";
                                          }
                                    ?><br/>
                                </div>
                            </div>
                            <div class="row rowodd" >
                                <div class="col-sm-6 col-lg-3">
                                    Select users to send Email and Emr messages:<?php 
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
                                   </select><br/>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    Default Text:<textarea name="store_text" id="store_text"><?php echo $store['text']; ?></textarea><br/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Email And Message Settings -->
                 <div class="accordion_in">
                    <div class="acc_head">Email And Message Settings</div>
                    <div class="acc_content">
                        <div class="container-fluid">
                            <div class="row rowodd" >
                               <fieldset>
                                    <div class="col-sm-9 col-lg-9" style="padding-bottom:5px;">
                                        <legend>Upload:</legend>
                                        Select users to send Email and Emr messages:
                                        <?php 
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
                                       </select>
                                    </div>   
                                    <div class="col-sm-5 col-lg-4">
                                        Default Text:<textarea name="upload_text" id="upload_text" ><?php echo $upload['text']; ?></textarea>
                                    </div>
                                    <div class="col-sm-4 col-lg-4">
                                        <input type="checkbox" class="upload_attach"  id="upload_attach"  name="upload_attach"  value="" <?php if($upload['attach']=='yes') echo "checked"; ?> onclick="check_status('upload_attach');"/>Do you want attachments for Email<br/>
                                    </div>
                                        
                                    </fieldset>   
                                
                            </div>
                            <div class="row roweven" >
                                <fieldset style="padding-top:3px;">
                                    <div class="col-sm-5 col-lg-9">
                                        <legend>Download:</legend>
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
                                       </select>
                                    </div> 
                                        <div class="col-sm-5 col-lg-4">
                                            Default Text:<textarea name="download_text" id="download_text" ><?php echo $download['text']; ?></textarea>
                                        </div>
                                      
                                    </fieldset> 
                                 
                            </div>
                            <div class="row rowodd" >
                                <fieldset style="padding-top:3px;">
                                    <div class="col-sm-9 col-lg-9" style="padding-bottom:5px;">
                                        <legend>Move Files:</legend>
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
                                               </select> 
                                    </div>    
                                    <div class="col-sm-5 col-lg-4">
                                        Default Text:<textarea name="move_text" id="move_text" ><?php echo $move['text']; ?></textarea>
                                    </div>
                                </fieldset> 
                                </div>
                        </div>
                    </div>
                </div>	
            </div>
            <input type="hidden" name="email"  id="email"  value="<?php echo $email; ?>" />
            <input type="submit" id="save" name="save" value="Save" class="btn btn-primary" />
        </form>   
        </div>    
        <div id="tab-2">
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
                        <th>Category</th>
                    </tr>
                </thead>
            </table> 
        </div>
        <div id="tab-3">
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
<!--                            <div id="imaginary_container"> -->
                                <div class="inner-addon left-inner-addon">
                                    <i class="glyphicon glyphicon-search"></i>
                                    <input type="text" class="form-control"  id="userSearch" placeholder="Search" >
                                </div>
<!--                            </div>-->
                        </div>
                        </div>
                </div>
                <div id="userscontenar">
                    <div>
                    </div>
                </div>
        </div>
        <div id="tab-4">
            <?php  
            //Drive access
               $sql_vis=sqlStatement("SELECT drive_access from tbl_user_custom_attr_1to1 where userid='".$_SESSION['authId']."'");
               $row1_vis=sqlFetchArray($sql_vis);
               $avail3 = [];
               if(!empty($row1_vis)) { 
                   $avail3=explode("|",$row1_vis['drive_access']);
               } 
            ?>
            <div class="accordion_close">
                <?php  if(in_array('patient',$avail3)) { ?>
                <div class="accordion_in">
                    <div class="acc_head">Patients</div>
                        <div class="acc_content"><div id='loading1' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
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
                                            <a href="#" class="btn btn-primary btn-sm" onclick="folder_creation('patient_folder_creation.php','1','create');">Run</a> <br>
                                        <?php } elseif ($cnt==0) { ?>
                                            <a href="#" class="btn btn-primary btn-sm" onclick="folder_creation('0','1','create');">Run</a> 
                                        <?php } ?>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
                <?php } ?>
                <div class="accordion_in">
                    <div class="acc_head">Users</div>
                        <div class="acc_content"><div id='loading2' style='display:none; '></div>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php 
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
                                            <a href="#" class="btn btn-primary btn-sm" onclick="folder_creation('user_folder_creation.php','2','create');">Run</a><br>
                                        <?php } elseif ($cnts==0) { ?>
                                            <a href="#" class="btn btn-primary btn-sm" onclick="folder_creation('0','1','create');">Run</a> 
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <?php  if(in_array('insurance',$avail3)){ ?>
                 <div class="accordion_in">
                    <div class="acc_head">Insurance</div>
                        <div class="acc_content"><div id='loading3' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
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
                                    if($cnt!=0) { ?>
                                        <a href="#" class="btn btn-primary " onclick="folder_creation('insurance_folder_creation.php','3','create');">Run</a><?php }elseif($cnt==0) {  ?>
                                        <a href="#" id='insr' class="btn btn-primary btn-sm" onclick="folder_creation('0','1','create');">Run</a><?php } 
                                    ?>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
                <?php } 
                if(in_array('pharmacy',$avail3)) { ?>
                <div class="accordion_in">
                    <div class="acc_head">Pharmacy</div>
                        <div class="acc_content"><div id='loading4' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
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
                                    if($cnt!=0) { ?>
                                        <a href="#" id='phr' class="btn btn-primary btn-sm" onclick="folder_creation('pharmacy_folder_creation.php','4','create');">Run</a>
                                    <?php } elseif ($cnt==0) { ?><a href="#" class="btn btn-primary btn-sm" onclick="folder_creation('0','1','create');">Run</a> <?php } ?>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
                <?php }  if(in_array('addrbk',$avail3)) { ?>
                <div class="accordion_in">
                    <div class="acc_head">Agency</div>
                        <div class="acc_content"><div id='loading5' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
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
                                        echo "please enter Agency folder Format"; 
                                    }if($cnt1!=0) { ?>
                                        <a href="#" id='addr' class="btn btn-primary btn-sm" onclick="folder_creation('addrbk_folder_creation.php','5','create');">Run</a>
                                    <?php } elseif ($cnt1==0) { ?><a href="#" class="btn btn-primary btn-sm" onclick="folder_creation('0','1','create');" >Run</a> <?php } ?>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
                <?php } ?>
                <div class="accordion_in">
                    <div class="acc_head">Facility</div>
                        <div class="acc_content"><div id='loading6' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
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
                                    if($cnt6!=0) { ?>
                                        <a href="#" id='fac2' class="btn btn-primary btn-sm" onclick="folder_creation('folder_creation/facility_folder_creation.php','6');">Run</a>
                                    <?php } elseif ($cnt6==0) { ?><a href="#" class="btn btn-primary btn-sm" onclick="folder_creation('0','1','create');" >Run</a> <?php } ?>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
            </div>    
        </div>
        <div id="tab-5">
            <div class="accordion_close">
                <div class="accordion_in">
                    <div class="acc_head">Patients</div>
                        <div class="acc_content"><div id='loading1' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
                                    Count of messages need to create: <?php echo "<div id='patients_1'>".  message_count($email,'patients'); ?><br/>
                                    <a href="#" class="btn btn-primary btn-sm" onclick="msg_creation('folder_creation/create_emr_msg.php','patients','1');">Run</a>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
                <div class="accordion_in">
                    <div class="acc_head">Users</div>
                        <div class="acc_content"><div id='loading2' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
                                    Count of messages need to create: <?php echo "<div id='users_2'>".  message_count($email,'users')."</div>"; ?><br/>
                                    <a href="#" class="btn btn-primary btn-sm" onclick="msg_creation('folder_creation/create_emr_msg.php','users','2');">Run</a>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
                <div class="accordion_in">
                    <div class="acc_head">Insurance</div>
                        <div class="acc_content"><div id='loading3' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
                                    Count of messages need to create: <?php echo "<div id='insurance_3'>".  message_count($email,'insurance')."</div>"; ?><br/>
                                    <a href="#" class="btn btn-primary btn-sm" onclick="msg_creation('folder_creation/create_emr_msg.php','insurance','3');">Run</a>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
                <div class="accordion_in">
                    <div class="acc_head">Pharmacy</div>
                        <div class="acc_content"><div id='loading4' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
                                    Count of messages need to create: <?php echo "<div id='pharmacy_4'>".  message_count($email,'pharmacy')."</div>"; ?><br/>
                                    <a href="#" class="btn btn-primary btn-sm" onclick="msg_creation('folder_creation/create_emr_msg.php','pharmacy','4');">Run</a>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
                <div class="accordion_in">
                    <div class="acc_head">Agency</div>
                        <div class="acc_content"><div id='loading5' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
                                    Count of messages need to create: <?php echo "<div id='address_Book_5'>". message_count($email,'address_Book')."</div>"; ?><br/>
                                    <a href="#" class="btn btn-primary btn-sm" onclick="msg_creation('folder_creation/create_emr_msg.php','address_Book','5');">Run</a>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
                <div class="accordion_in">
                    <div class="acc_head">Facility</div>
                        <div class="acc_content"><div id='loading6' style='display:none; '></div>
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-sm-6">
                                    Count of messages need to create: <?php echo "<div id='facility_6'>". message_count($email,'facility')."</div>"; ?><br/>
                                    <a href="#" class="btn btn-primary btn-sm" onclick="msg_creation('folder_creation/create_emr_msg.php','facility','6');">Run</a>
                                </div>
                            </div>
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
 <script type="text/javascript">
        $(document).ready(function () {
            var $tabs = $('#horizontalTab');
            $tabs.responsiveTabs({
                rotate: false,
                startCollapsed: 'accordion',
                collapsible: 'accordion',
                setHash: true,
                //disabled: [3,4],
                click: function(e, tab) {
                    $('.info').html('Tab <strong>' + tab.id + '</strong> clicked!');
                },
                activate: function(e, tab) {
                    $('.info').html('Tab <strong>' + tab.id + '</strong> activated!');
                },
                activateState: function(e, state) {
                    //console.log(state);
                    $('.info').html('Switched from <strong>' + state.oldState + '</strong> state to <strong>' + state.newState + '</strong> state!');
                }
            });
            $(".accordion_close").smk_Accordion({
                    closeAble: true, //boolean
            });

           // $('.acc_content').not('.nodemohtml').html('<p>Quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Fusce aliquet neque et accumsan fermentum. Aliquam lobortis neque in nulla  tempus, molestie fermentum purus euismod.</p>');
        });
        
    </script>
    <?php include '../section_footer.php'; ?>
</body>
</html>

