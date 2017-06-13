<?php
include_once("../../globals.php");
$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';
$portal_user=$_SESSION['authUser'];

//$parent = $_REQUEST['parent']; 
$folder = $_REQUEST['folder']; 
$file_id=$_REQUEST['file_id'];
$pid=$_REQUEST['pid'];

$category_arr= array(
        array('title' =>'patients','fieldid'=>'parent_folder'),
        array('title' =>'users','fieldid'=>'user_parent_folder'),
        array('title' =>'insurance','fieldid'=>'ins_parent_folder'),
        array('title' =>'pharmacy','fieldid'=>'pharmacy_parent_folder'),
        array('title' =>'agency','fieldid'=>'addrbk_parent_folder'),
        array('title' =>'facility','fieldid'=>'facility_parent_folder')
);

//linked folders to the providers
$sel_link=sqlStatement("select * from tbl_allcare_userfolder_links where user_id=".$_SESSION['authId'] );
$row_link=sqlFetchArray($sel_link);


 $mime_types= array(
        "xls" =>'application/vnd.ms-excel',
        'application/pdf'=> "../../../images/pdf-icon.png" ,
        'application/vnd.google-apps.folder'=>'../../../images/folder.jpg'
    );
// to get configured email
$sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
$row = sqlFetchArray($sql);
    //Drive access
   $sql_vis=sqlStatement("SELECT drive_access,allcare_obj from tbl_user_custom_attr_1to1 where userid='".$_SESSION['authId'] ."'");
   $row1_vis=sqlFetchArray($sql_vis);
   $avail3 = [];
   if(!empty($row1_vis)) { 
       $avail3=explode("|",$row1_vis['drive_access']);
       $avail_cat=explode("|",$row1_vis['allcare_obj']);
   }
   
//parent folder  
$selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "'");
$sel_rows = sqlFetchArray($selection);
foreach ($category_arr as $key => $item) {
    if(in_array($item['title'],$avail_cat)){
        $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows[$item['fieldid']]);
        $category_arr1[$key]['title'] = $item['title'];
        $category_arr1[$key]['fieldid'] = $item['fieldid'];
        $category_arr1[$key]['rootid'] = $parentid;
     }  
}

/*if($_REQUEST['folder']==''){
    $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['parent_folder']);
}else {
    $parentid =$_REQUEST['folder'];
}*/
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://';
$_GLOBALS["parentid"] = $parentid;
$foulderdata = $protocol . $_SERVER['HTTP_HOST'] . '/api/DriveSync/listallfilespagetoken_web/' . $row['notes'];
$foulderinfo =  $protocol . $_SERVER['HTTP_HOST'] . '/api/DriveSync/getfileinfo_web/' . $row['notes'];  
$rename_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/renameFile_web/emr/'.$_SESSION['authUser'].'/';
$delete_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/deletefile_web/'.$row['notes'].'/emr/'.$_SESSION['authUser'].'/';
$addfolder_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes']."/";
$move_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/movefile_web/'.$row['notes'].'/'.trim($file_id," ").'/';
$fileview_url=$protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/downloadfile_web/'.$row['notes'].'/';
$fileupload_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/uploadfile_web/'.$row['notes'].'/emr/'.$_SESSION['authUser'].'/';
$filedownload_url = $protocol.$_SERVER['HTTP_HOST'].'/interface/main/allcarereports/download_file.php?file_id=';
$viewfile=$protocol.$_SERVER['HTTP_HOST'].'/interface/main/allcarereports/view_file.php?file_id=';
$unsharefile = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/delete_permission/'.$row['notes'].'/';
$parentlink_url = $protocol.$_SERVER['HTTP_HOST']."/interface/patient_file/summary/link_folders_save.php";
$childunlink_url = $protocol.$_SERVER['HTTP_HOST']."/interface/patient_file/summary/unlink_folders_save.php";
?>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" href="../../patient_file/summary/driveassets/css/lity.css">
        <link rel="stylesheet" href="../../patient_file/summary/driveassets/css/uploadfile.css">
        <link rel="stylesheet" href="../../patient_file/summary/driveassets/drive.css">
      
        <link rel="stylesheet" href="../../patient_file/summary/driveassets/css/jquery.webui-popover.min.css">
        <link rel="stylesheet" href="./css/drivecustom.css">
<!--        <link rel="stylesheet" href="./driveassets/css/linkfoulder.css">-->
       
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <script src="../../patient_file/summary/driveassets/js/jquery.webui-popover.min.js"></script>
        <script src="../../patient_file/summary/driveassets/js/lity.js"></script>
        <style>
          @media screen and (max-width: 767px) {

                main#content {
                  margin-top: 65px;
                  transition: all ease-out 0.3s;
                }
                .entry .entry_link img{
                    width: 100px;
                }
                .entry_block{
                    width: 103px !important;
                }
                .uyd-grid .entry_thumbnail {
                    height: 90px;
                }

                .file .entry_thumbnail .entry_link img {
                    height: 88px;
                    width: 89px;
                }

            }
 
            //table for f2f
            .showborder {
                width:650px;
            }
            .showborder td {
                border-bottom:1px dashed #000000;
                text-align:left;
                //font-size:9pt;
                height:40px;
            }
            .showborder_head  th {
                border-bottom:1px solid #000000;
                text-align:left;
                //font-size:9pt;
            }
            .shownoborder td {
                text-align:left;
                //font-size:9pt;
                height:40px;
            }
            .showborder_long {
                width:100%;
            }
            .showborder_long tr td{
                border-bottom:1px dashed #000000;
                text-align:left;
                height:40px;
                //font-size:9pt;
            }

            .navbar-nav > li > .dropdown-menu{
                /*margin-top: 4px !important;*/
            }
            .uyd-grid .dropdown-menu{
                background-color: #fff;
                padding: 5px 0px;
            }
            .webui-popover .dropdown-menu {
                background-color: #fff;
                padding: 5px 0px;
            }

            .webui-popover-content .dropdown-menu > li > a {
                padding: 3px 10px;
            }
            .webui-popover-content .dropdown-menu > li > a:hover {
                color:#fff;
                background-color:#0E92C7;
            }
            .webui-popover-content .dropdown-menu{
               min-width: 0px;
            }
            .webui-popover-content .dropdown-menu > li > a span.glyphicon{
                padding-right: 8px;
            }

            @media screen and (max-width : 320px){
                .filename,.filesize, .status {float: none;display: inline-block;margin-left: 9px;}
            }
            #services {
                margin-bottom: -45px;
            }
            /* Required field END */
            .select2{
                width: 100%;
            }
            .select2-results .select2-highlighted {
                background: #3875d7 none repeat scroll 0 0;
                color: #fff !important;
            }
            .select2-results .select2-highlighted .select2-result-label{
                color: #fff;
            }
            
            //side bar style
            
            /* side menu*/
            .navbar-default {
                border-color: transparent;
            }

            .page-content > .row{
                margin-left:0px;
                margin-right:0px;
                min-height: 100%;
                margin-bottom: -45px;
            }

            #wrapper {
                padding-left: 53px;
                transition: all .4s ease 0s;
                height: 100%;
                padding-top: 0px;
                margin-top: 0px;
                height: 100%
            }

            #sidebar-wrapper {
                margin-left: -235px;
                left: 53px;
                width: 235px;
                background: #46a1b4;//#222;
                position: fixed;
                height: 100%;
                z-index: 3;
                transition: all .4s ease 0s;
            }

            .sidebar-nav {
                display: block;
                float: left;
                width: 235px;
                list-style: none;
                margin: 0;
                padding: 0;
                text-transform:capitalize;
            }
            .sidebar-nav li{
                display: inline;
            }
            .page-content{
                min-height: 100%;
            }
            #page-content-wrapper {
                padding-left: 0;
                margin-left: 0;
                width: 100%;
                min-height: 100%;
                height: auto;
                margin-bottom: -45px;
            }
            #wrapper.active {
                padding-left: 235px;
            }
            #wrapper.active #sidebar-wrapper {
                left: 235px;
            }

            #page-content-wrapper {
              width: 100%;
              min-height: 100%;
            }
            #sidebar {
                height: 100%;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }


            #sidebar_menu li a, .sidebar-nav li a {
                color: #fff; //#999;
                display: block;
                float: left;
                text-decoration: none;
                width: 235px;
                background: #46a1b4;//#252525;
                border-top: 1px solid #54afc2;//#373737;
                border-bottom: 1px solid #3893a6; //#1A1A1A;
                -webkit-transition: background .5s;
                -moz-transition: background .5s;
                -o-transition: background .5s;
                -ms-transition: background .5s;
                transition: background .5s;
            }
            .sidebar_name {
                padding-top: 25px;
                color: #fff;
                opacity: .7;
            }

            .sidebar-nav li {
              line-height: 40px;
              text-indent: 15px;
            }

            .sidebar-nav li a {
              color: #fff;//#999999;
              display: block;
              text-decoration: none;
            }

            .sidebar-nav li a:hover {
              color: #fff;
              background: rgba(255,255,255,0.2);
              text-decoration: none;
              cursor: pointer;
            }
            
            .sidebar-nav li a.active {
                background-color: rgba(76, 214, 245, 0.62);
                cursor: default;
            }
            
            .sidebar-nav li a:active,
            .sidebar-nav li a:focus {
              text-decoration: none;
            }

            .sidebar-nav > .sidebar-brand {
              height: 45px;
              line-height: 45px;
              font-size: 18px;
            }

            .sidebar-nav > .sidebar-brand a {
              color: #999999;
            }

            .sidebar-nav > .sidebar-brand a:hover {
              color: #fff;
              background: none;
            }

            #main_icon
            {
               float:right;
               padding-right: 20px;
               padding-top:13px;
            }
            .sub_icon
            {
                float:right;
               padding-right: 20px;
               padding-top:10px;
            }
            .content-header {
              height: 65px;
              line-height: 65px;
            }

            .content-header h1 {
              margin: 0;
              margin-left: 20px;
              line-height: 65px;
              display: inline-block;
            }

            @media (max-width:767px) {
                #wrapper {
                    padding-left: 53px;
                    transition: all .4s ease 0s;
                }
                #sidebar-wrapper {
                    left: 53px;
                }
                #wrapper.active {
                    padding-left: 53px;
                }
                #wrapper.active #sidebar-wrapper {
                    left: 235px;
                    width: 235px;
                    transition: all .4s ease 0s;
                }
            }
            #nopermissions{
                font-size: 1.4em;
                font-weight: bold;
                text-align: center;
            }
            .left-a.glyphicon {
                left: 90px;
            }
            .webui-popover-content > div{
                background-color: rgb(255, 255, 255); 
                border-radius: 6px; 
                box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2); 
                border: 1px solid rgba(0, 0, 0, 0.2);
            }
            .webui-popover{
                background-color:transparent;
                border: 0;
                border-radius: 0;
                box-shadow: none;
                height: 120px;
            }
        </style>

        <script type='text/javascript'>
            var rootfoulder = '<?php echo $parentid ?>';
            var driveopt = ('<?php echo $row1_vis['drive_access'] ?>').split("|");
            var linked_folders=null;
            var unlinked_folders=null;
            var parentid = "";
            var progressid = "";
            var targetfid = "";
            var timeout = null;
            var filemovie = true;
            var files=["foulder_icon.png","fileloader.gif","foulder_add_icon.png","drive.png","reload.png","defaultfile.png","File-Audio-icon.png","zip-con.png","xml-icon.png","drag-upload.png"]
            var currentajaxcall = null;
            var allajaxcalls = [];
            var viewmimetypesfile = ["text/plain","text/html","application/x-httpd-php","image/jpeg","image/png","application/pdf","image/gif","image/bmp"]
            var viewmimetypesdocs = ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/vnd.google-apps.document","application/vnd.google-apps.spreadsheet","application/msword","text/csv","application/rar","application/zip","application/ogg","image/x-photoshop","application/postscript"];
            var islightboxopen = true;
            var currentfileid = "",unsharelink="";
            var isrootfolder = true;
            var userid = '<?php echo $_SESSION['authId']; ?>';
            var category = null ;
            var listdata = null;
            var listsetintervel=null;
            var testexitremoveopt = {"texteffects":false,"aligneffects":false,"textformats":false,"fonteffects":false,"actions":false,"insertoptions":false,"extraeffects":false,"advancedoptions":false,"screeneffects":false,"ol":false,"ul":false,"undo":false,"redo":false,"l_align":false,"r_align":false,"c_align":false,"justify":false,"insert_img":false,"hr_line":false,"block_quote":false,"source":false,"strikeout":false,"indent":false,"outdent":false,"fonts":false,"styles":false,"print":false,"rm_format":false,"status_bar":true,"font_size":false,"splchars":false,"insert_table":false,"select_all":false}
            var portaluser = '<?php echo $_SESSION['authUser']; ?>'
            var saveobj = {};
            var interest = null;
            var allfilesdata = [];
            var menuitems = null;
            loadimages(files);
            function loadimages(files){
                var filelength = files.length;
                 for (var i = 0; i < filelength; i++) 
                 {
                    var theImage = new Image();
                    theImage.src = "../../patient_file/summary/driveassets/driveimages/" +files[i];
                 }
            }
            $(function(){
                $('#sidebar').append(function(){
                    var html = "";
                    menuitems = JSON.parse('<?php echo json_encode($category_arr1) ?>');
                    $.each(menuitems,function(index,value){
                        var itemname = value['title'].replace(/_/g," ");
                        html += "<li id='"+itemname+"'><a href='#' data-category='"+value['title']+"' data-frame='"+value['rootid']+"'>"+itemname+"<span class='sub_icon glyphicon glyphicon-link'></span></a></li>";
                    });
                    return html;
                });

            $('#sidebar li a').first().addClass("active");
                   $("#services").css("min-height",window.innerHeight+"px");
                   var barwidth = "100%";
                   if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1){
                        barwidth = "96%";
                   }
                    var settings = {
                    url: '<?php echo $fileupload_url; ?>'+rootfoulder+"/"+interest,
                    method: "POST",
                    fileName: "myfile",
                    uploadButtonClass:"fileupload-buttonbar",
                    wrapdivdrage:$('.list-container'),
                    dragDropStr:"",
                    uploadStr:"",
                    showAbort: false,
                    showFileSize:false,
                    fileCounterStyle:") ",
                    statusBarWidth: barwidth,
                    dragdropWidth: barwidth,
                    autoSubmit:true,
                    formData: {"stetas":false},
                    onSelect:function(files){
                        /*swal({
                            title: "confirm message!",
                            text: "Do you want to send message.",
                            showCancelButton: true,
                            closeOnConfirm: true,
                            animation: "slide-from-top"
                          },
                          function(isConfirm){
                             if(isConfirm){
                                $("#addmessagemodal").modal("show");
                            }else{
                                fileuploader.startUpload();
                            }
                          });*/
                    },
                    onSubmit:function(files, xhr)
                    {  
                       $('.fileupload-drag-drop').hide();
                    },
                    customProgressBar: function(obj,s)
                    {
                        this.statusbar = $("<div class='template-upload'></div>");
                        this.preview = $("<img class='ajax-file-upload-preview' />").width(s.previewWidth).height(s.previewHeight).appendTo(this.statusbar).hide();
                        this.filename = $("<div class='ajax-file-upload-filename'></div>").appendTo(this.statusbar);
                        this.progressDiv = $("<div class='upload-progress'>").appendTo(this.statusbar).hide();
                        this.progressbar = $("<div class='ui-progressbar-value'></div>").appendTo(this.progressDiv);
                        this.abort = $("<div>" + s.abortStr + "</div>").appendTo(this.statusbar).hide();
                        this.cancel = $("<div>" + s.cancelStr + "</div>").appendTo(this.statusbar).hide();
                        this.done = $("<div>" + s.doneStr + "</div>").appendTo(this.statusbar).hide();
                        this.download = $("<div>" + s.downloadStr + "</div>").appendTo(this.statusbar).hide();
                        this.del = $("<div>" + s.deletelStr + "</div>").appendTo(this.statusbar).hide();

                        this.abort.addClass("custom-red");
                        this.done.addClass("custom-green");
                        this.download.addClass("custom-green");            
                        this.cancel.addClass("custom-red");
                        this.del.addClass("custom-red");

                        return this;
                    },
                     onSuccess:function(files,data,xhr,pd)
                    {
                        pd.statusbar.find('.ajax-file-upload-filename .status').html("<span class='glyphicon glyphicon-ok'></span>Success").addClass("statussuccess");
                    },
                    afterUploadAll:function(obj)
                    {
                        $(".nav-refresh").click()
                    },
                    onError: function(files,status,errMsg,pd)
                    {	pd.statusbar.find('.ajax-file-upload-filename .status').addClass("statuserror");
                    }
                }
                
                function mergeObjects(objectArray) {
                    if (objectArray.length) {
                        var b = "", i = -1;
                        while (objectArray[++i]) {
                            var str = JSON.stringify(objectArray[i]);
                            b += str.slice(1, str.length - 1);
                            if (objectArray[i + 1]) b += ",";
                        }
                        return JSON.parse("{" + b + "}");
                    }
                    return {};
                }

                var fileuploader = $("#mulitplefileuploader").uploadFile(settings);
                $('#sidebar li > a').on("click",function(){
                    if(currentajaxcall != null){
                        currentajaxcall.abort();
                    }
                    $('.overlay').show("fast");
                    $('.overlay .loader-contenar').show();
                    $(this).parents('ul').find('li a').removeClass('active');
                    $(this).addClass("active");
                    rootfoulder = $(this).data('frame');
                    category = $(this).data('category');
                    $("#searchtype").val("all");
                    $("#searchentery").val("");
                    $(".ajax-file-upload-container").empty();
                    loaddrive(rootfoulder,rootfoulder);
                });

                if(menuitems != null){
                    $('#sidebar li > a').first().trigger("click");
                }else{
                    $('.overlay').hide();$('.overlay .loader-contenar').hide();
                    $(".layout-grid").append("<p id='nopermissions'>No permissions for view folders contact your administrator.</p>")
                }
                   $('a.nav-search').webuiPopover('destroy').webuiPopover({trigger:'click',padding:0,width:320});	
                   function ajaxcall(url,data,type,callback,errorcallback){
                      currentajaxcall = $.ajax({url:url,type:type,xhrFields: {withCredentials: true},data:data, crossDomain: true,error:errorcallback,success:callback});
                      allajaxcalls.push(currentajaxcall);
                   }
                  //loaddrive(rootfoulder,rootfoulder);
                  function loaddrive(rootfoulder,folderid){
                    interest = $('#sidebar li a.active').data('category');
                    progressid = folderid;
                    var folderinfourl = '<?php echo $foulderinfo?>'+"/"+folderid;
                    var gerfilesurl = '<?php echo $foulderdata?>'+"/"+folderid+"/all";
                    var getfolders = '<?php echo $foulderdata?>'+"/"+folderid+"/folders";
                    //console.log(gerfilesurl);
                    ajaxcall('<?php echo $parentlink_url ?>',{category:category,userid:userid},"post",function(data){ linked_folders = $.parseJSON(data)},ajaxdefaulterrorcallback,false);
                    ajaxcall('<?php echo $childunlink_url ?>',{category:category,userid:userid},"post",function(data){ unlinked_folders = $.parseJSON(data)},ajaxdefaulterrorcallback,false);
                    
                    ajaxcall(folderinfourl,null,"get",getfileinfo,ajaxdefaulterrorcallback);
                     function getfileinfo(data){
                         
                        var folderinfodata = $.parseJSON(data);
                        if(folderinfodata[0] != undefined){
                            if(folderinfodata[0].error_code == "403"){
                                setTimeout(ajaxcall(folderinfourl,null,"get",getfileinfo,ajaxdefaulterrorcallback),3000);
                                return;
                            }
                        }
                        if(rootfoulder != folderinfodata['id']){
                            parentid = folderinfodata['parents'][0];
                            var getparentdataurl = '<?php echo $foulderinfo?>'+"/"+folderinfodata['parents'][0];
                            isrootfolder = false;
                            ajaxcall(getparentdataurl,null,"get",function(parentdata){
                                // comment code for linkedfolder code
                                //ajaxcall(getfolders+"/empty",null,"get",function(foldersdata){
                                    ajaxcall(gerfilesurl+"/empty",null,"get",function(filesdata){getfiles(filesdata,'[]');},ajaxdefaulterrorcallback);
                                //},ajaxdefaulterrorcallback);
                                var parentdata = $.parseJSON(parentdata);
                                $('.nav-title').html('<a href="javascript:void(0)" class="folder" data-id="'+parentdata["id"]+'">'+parentdata["name"]+'</a> &lsaquo;&lsaquo; '+
                                        '<a href="javascript:void(0)" class="folder" data-id="'+folderinfodata["id"]+'"><strong>'+folderinfodata["name"]+'</strong></a>');
                            },ajaxdefaulterrorcallback);
                        }else{
                           //isrootfolder = true;
                           isrootfolder = false;
                           // comment code for linkedfolder code
                           //ajaxcall(getfolders+"/empty",null,"get",function(foldersdata){
                                    ajaxcall(gerfilesurl+"/empty",null,"get",function(filesdata){getfiles(filesdata,'[]');},ajaxdefaulterrorcallback);
                                //},ajaxdefaulterrorcallback);
                            $('.nav-title').html('<a href="javascript:void(0)" class="folder" data-id="'+folderinfodata["id"]+'"><strong>'+folderinfodata["name"]+'</strong></a>');
                        }
                     }
                     function getfiles(data){
                        // console.log( "foulderfiles: " + data);
                         var getfileinfo = '<?php echo $foulderinfo?>';
                         //var folders = $.parseJSON(foldersdata)
                         var files = $.parseJSON(data);
                        // console.log(JSON.stringify(linked_folders) + " links : " + JSON.stringify(unlinked_folders))
                         //var files = findlinkedfolders(files,folders,linked_folders,unlinked_folders,isrootfolder);
                         //console.log(files);
                         if(files[0] != undefined){
                            if(files[0].error_code == "403"){
                                var fieldurl = this.url == undefined?gerfilesurl+"/empty":this.url;
                                setTimeout(ajaxcall(fieldurl,null,"get",getfiles,ajaxdefaulterrorcallback),3000);
                                return;
                            }
                        }
                        var pagetocken = files.nextpageToken;
                        delete files.nextpageToken;
                        //$.extend(allfilesdata,files);
                        //console.log(pagetocken);
                        allfilesdata.push(files)
                        if(pagetocken != null){
                            ajaxcall(gerfilesurl+"/"+pagetocken,null,"get",getfiles,function(xhr, ajaxOptions, thrownError){
                                console.log(xhr.status);
                                console.log(thrownError);
                                console.log(xhr.responseText);
                                if($.trim(thrownError) =="abort"){
                                    allfilesdata = [];
                                }else{
                                    $('.loader-contenar').hide();
                                    $('.reload').show();
                                }
                            });
                        }else{
                            files = $.map(allfilesdata,function(value,index){
                                return $.map(value,function(v,i){return v});
                            });
                            allfilesdata = [];
                        }
                        
                        var files =  $.map(files,function(value,index){return value;});
                        $('.layout-grid').empty();
                         if($.inArray( "create_folder", driveopt ) !=-1 && !isrootfolder)
                            files.unshift({name:"Add folder",id:"",mimeType:"application/addfoulder",thumbnail:"../../patient_file/summary/driveassets/driveimages/foulder_add_icon.png",opt:false});
                         if(rootfoulder !=folderid)
                            files.unshift({name:"Previous folder (My Drive)",id:parentid,mimeType:"application/pre",thumbnail:"../../patient_file/summary/driveassets/driveimages/foulder_icon.png",opt:false});

                        var fileopt = ["<a href='javascript:void(0)' id='previewfile'><span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span>Preview</a>"];
                        var folderopt = !filemovie?["<a href='javascript:void(0)' data-toggle='modal' data-target='#movefile'><span class='glyphicon glyphicon-check' aria-hidden='true'></span>Select Folder</a>"]:[];

                        $.each(driveopt,function(index,value){
                            if(value == "rename"){
                                fileopt.push("<a href='javascript:void(0)' data-toggle='modal' data-target='#rename'><span class='glyphicon glyphicon-tag' aria-hidden='true'></span>"+value.charAt(0).toUpperCase()+value.slice(1)+"</a>");
                                folderopt.push("<a href='javascript:void(0)' data-toggle='modal' data-target='#rename'><span class='glyphicon glyphicon-tag' aria-hidden='true'></span>"+value.charAt(0).toUpperCase()+value.slice(1)+"</a>");
                            }else if(value == "delete"){
                                fileopt.push("<a href='javascript:void(0)' data-toggle='modal' data-target='#delete'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span>"+value.charAt(0).toUpperCase()+value.slice(1)+"</a>");
                                folderopt.push("<a href='javascript:void(0)' data-toggle='modal' data-target='#delete'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span>"+value.charAt(0).toUpperCase()+value.slice(1)+"</a>");
                            }else if(value == "download"){
                                fileopt.push("<a href='javascript:void(0)' class='downloadfile'><span class='glyphicon glyphicon-cloud-download' aria-hidden='true'></span>"+value.charAt(0).toUpperCase()+value.slice(1)+"</a>");
                            }
                        });

                    //code for create entery html
                         addCustomThumbnail(files);
                        var html = "";
                         $.each(files,function(index,value){
                                var opt = getfiletype(value['mimeType'])=="file"?fileopt:folderopt;
                                
                                var patt = new RegExp($("#searchentery").val(),"i");
                                var patt1 = new RegExp($("#searchtype").val(),"i");
                                var res = patt.test(value['name']);
                                var res1 = patt1.test(value['mimeType']);
                                var filtertype = null;
                                if($("#searchtype").val() != "all"){
                                   filtertype = (res==true && res1 == true);
                                }else{
                                   filtertype =  res;
                                }
                                
                                var style = filtertype == false?"none":"inline-block";
                        
                                  html += '<div class="entry '+getfiletype(value['mimeType'])+'" data-id="'+value['id']+'" data-type="'+value['mimeType']+'" data-name="'+value['name']+'" style="display:'+style+'">\n\
                                                <div class="entry_block" style="width: 131px;">';
                                  if(getfiletype(value['mimeType']) != "parentfolder" && getfiletype(value['mimeType']) != "newfolder"){
                                      html +='<div class="entry_edit"><a class="entry_edit_menu show-pop-dropdown" data-placement="bottom-left">\n\
                                                <i class="glyphicon glyphicon-chevron-down"></i></a>';
                                      html +='<div  class="webui-popover-content"><ul class="dropdown-menu" >';
                                      $.each(opt,function(i,v){
                                              html +='<li data-name="'+value['name']+'" data-id="'+value['id']+'" data-type="'+value['mimeType']+'">'+v+'</li>';
                                          });
                                       html += '</ul></div>\n\
                                                </div>';
                                  }           
                                   html +='<div class="entry_thumbnail">\n\
                                            <div class="entry_thumbnail-view-bottom">\n\
                                                <div class="entry_thumbnail-view-center">\n\
                                                    <a class="entry_link"><img class="" src="'+value.thumbnail+'" onerror="imgError(this);"/></a>\n\
                                                </div>\n\
                                            </div>\n\
                                         </div>';
                                  html +='<div class="entry_name" title="'+value['name']+'">\n\
                                            <a class="entry_link">\n\
                                                <div class="entry-name-view">\n\
                                                    <span>\n\
                                                        <label for="select">'+value['name']+'</label>\n\
                                                    </span>\n\
                                                </div>\n\
                                            </a>\n\
                                         </div>';   
                                  html +="</div></div>";
                         });
                         $('.layout-grid').html(html);
                         $('.overlay').hide("fast");
                         $('.newfolder').insertAfter($(".layout-grid .folder").last());
                         $('.parentfolder,.newfolder').show();
                         if(getMobileOperatingSystem() == "Windows Phone" || getMobileOperatingSystem() == "Android" || getMobileOperatingSystem() == "iOS")
                           $entery.find('.entry_block .entry_edit').css({opacity:1,background:"transparent"});
                         var show = false;
                         $('a.show-pop-dropdown').webuiPopover('destroy').webuiPopover({placement:'top-left',padding:0,width:"auto",onShow: 
                            function($element) {
                                clearTimeout(timeout);
                                var elementid = $element.attr('id');
                                      $('a.show-pop-dropdown').filter(function( index ,value) {
                                        var elementattr = $(value).attr("data-target");
                                        if(elementattr == elementid){
                                           $(value).parents('.entry').addClass('popupopen');
                                        }
                                      });

                                    $element.hover(
                                            function(){
                                                show = true;
                                            },
                                            function(){
                                                clearTimeout(timeout);
                                                $('.entry').removeClass('popupopen');$('a.show-pop-dropdown').webuiPopover('hide');show = false;
                                            }
                                    );
                            },
                            onHide:function(){clearTimeout(timeout);$('.entry').removeClass('popupopen')}});
                        $('a.show-pop-dropdown').mouseleave(function(){
                            timeout = setTimeout(function(){
                                if(show==false){
                                    $('.entry').removeClass('popupopen');$('a.show-pop-dropdown').webuiPopover('hide');
                                }
                            },1000);

                        });

                         // file upload plugin options update
//                        console.log('<?php echo $fileupload_url; ?>'+progressid+"/"+interest);
                        fileuploader.update({url:'<?php echo $fileupload_url; ?>'+progressid+"/"+interest});
                        //$('.fileupload-drag-drop').show();
                        //fileuploader.reset();
                     }
                  }

                  function findlinkedfolders(allfiles,folders,linkedfolders,unlinkedfolders,isrootfolder){
                    var getidsfl = $.map(folders,function(v,i){
                        return v['id'];
                    });
                    var newfilesArray = $.grep(allfiles, function(v) {
                        if(getidsfl.indexOf(v['id']) !=-1)
                            return false;
                        return true;
                    });

                    var getlinkeddata = $.grep(folders, function(v) {
                        if(($.inArray(v['id'],$.map(linkedfolders,function(vl,i){return vl;})) !=-1 && isrootfolder) || ($.inArray(v['id'],$.map(unlinkedfolders,function(vl,i){return vl;})) ==-1 && !isrootfolder))
                            return true;
                    });

                   newfilesArray = getlinkeddata.concat(newfilesArray);
                    return newfilesArray;
                  }

                // default ajax error call back;               
                var ajaxdefaulterrorcallback = function(xhr, ajaxOptions, thrownError){
                    console.log(xhr.status);
                    console.log(thrownError);
                    console.log(xhr.responseText);
                    if($.trim(thrownError) !="abort"){
                         $('.loader-contenar').hide();
                         $('.reload').show();
                     }
                };

                $('.nav-home').click(function(){
                    clearajaxcalls(allajaxcalls);
                     $('.overlay').show("fast");
                     loaddrive(rootfoulder,rootfoulder);
                });
                $('.nav-refresh').click(function(){
                    clearajaxcalls(allajaxcalls);
                    $('.overlay').show("fast");
                    loaddrive(rootfoulder,$('.nav-title a').last().data('id'));
                });
                $(document).on("click",".folder,.parentfolder",function(){
                     clearajaxcalls(allajaxcalls);
                     $('.overlay').show("fast");
                     $("#searchtype").val("all");
                     $("#searchentery").val("");
                     fileuploader.reset();
                     $(".fileupload-drag-drop").css("display","block");
                     loaddrive(rootfoulder,$(this).data('id'));
                });
               $('.reload').click(function(){
                    clearajaxcalls(allajaxcalls);
                     $('.overlay').show("fast");
                     $('.overlay .loader-contenar').show();
                     $(this).hide();
                     loaddrive(rootfoulder,progressid);
               });
               // add out custome thumbnailLinks on files*/
                function addCustomThumbnail(data){
                   // console.log(data)
                    $.each(data,function(index,value){
                        if((value.mimeType).toLowerCase()=="application/wav" || (value.mimeType).toLowerCase()=="application/aac" || (value.mimeType).toLowerCase()=="application/mp3" || (value.mimeType).toLowerCase()=="application/mpeg" || (value.mimeType).toLowerCase()=="application/ogg" || (value.mimeType).toLowerCase()=="application/wav" || (value.mimeType).toLowerCase()=="application/webm" || (value.mimeType).toLowerCase()=="application/aiff" )
                        {
                           value.thumbnail = "../../patient_file/summary/driveassets/driveimages/File-Audio-icon.png";

                        }if((value.mimeType).toLowerCase()=="application/zip")
                        {
                           value.thumbnail = "../../patient_file/summary/driveassets/driveimages/zip-con.png";

                        }if((value.mimeType).toLowerCase()=="application/rar")
                        {
                           value.thumbnail = "../../patient_file/summary/driveassets/driveimages/rar-con.png";

                        }if((value.mimeType).toLowerCase()=="text/xml")
                        {
                           value.thumbnail = "../../patient_file/summary/driveassets/driveimages/xml-icon.png";

                        }else if ((value.mimeType).toLowerCase() == "application/vnd.google-apps.folder")
                        {
                           value.thumbnail = "../../patient_file/summary/driveassets/driveimages/foulder_icon.png"

                        }else if(value.thumbnail == null)
                        {
                            value.thumbnail = "https://drive.google.com/thumbnail?id=" + value.id;
                        }
                    });

                }
                /* set entery type on files*/
                function getfiletype(minetype){
                      if(minetype=="application/vnd.google-apps.folder"){
                          return "folder";
                      }else if(minetype=="application/pre"){
                            return "parentfolder";
                      }else if(minetype=="application/addfoulder"){
                           return "newfolder";
                      }else if(minetype == undefined){
                          return "loader";
                      }else{
                           return "file";
                      }
                }

                /* clear all ajaxcalls when runing*/

                function clearajaxcalls(callslist){
                    $.each(callslist,function(index,value){
                        value.abort();
                    })
                   // console.clear();
                    callslist = []
                }

                $('#rename,#delete,#movefile').on("show.bs.modal",function(event){
                   $('a.show-pop-dropdown').webuiPopover('hide');
                    var target = $(event.relatedTarget);
                    var correntname = target.parent('li').data('name');
                    targetfid =  target.parent('li').data('id');
                    var modal = $(this);
                    if(modal.attr("name") == "rename")
                    {
                       modal.find('#renamebox').val(correntname);

                    }else if(modal.attr("name") == "delete")
                    {
                       modal.find('#deletemessage em').html(correntname);
                    }

                });
                $('#renamebtn').on("click",function(){
                   
                    clearajaxcalls(allajaxcalls);
                    $('.overlay').show("fast");
                    $('#rename').modal('hide');
                    var renamelink = '<?php echo $rename_url ?>'+category+"/"+targetfid+"/"+$('#renamebox').val()+"/"+'<?php echo $row['notes'] ?>';
                    ajaxcall(renamelink,null,"get",function(data){
                         loaddrive(rootfoulder,progressid);
                    },ajaxdefaulterrorcallback);
                })

                $('#deletebtn').on("click",function(){
                    clearajaxcalls(allajaxcalls);
                    $('.overlay').show("fast");
                    $('#delete').modal('hide');
                    var deletedlink = '<?php echo $delete_url ?>'+category+"/"+targetfid;
                    ajaxcall(deletedlink,null,"get",function(data){
                         loaddrive(rootfoulder,progressid);
                    },ajaxdefaulterrorcallback);
                });
                $(document).on("click",'.downloadfile',function(){
                    var filename = $(this).parent().data("name");
                    var id = $(this).parent().data("id");
                    var type = $(this).parent().data("type");
                    url = '<?php echo $filedownload_url; ?>'+id+"&type="+type+"&name="+filename+"&category="+category;
                    $('body').append("<iframe src='"+url+"' frameborder='0' onload='downloadframelc(this)'></iframe>");
                });


                $(document).on("click",".newfolder",function(){
                    $('a.show-pop-dropdown').webuiPopover('hide');
                    $('#addfoulder').modal('show');
                })
                
                $("#savebtn").click(function(event){
                    var self = $(this);
                    if($(this).parents(".modal-content").find(".modal-body #addto").select2("val") == "" || $(this).parents(".modal-content").find(".modal-body #addmessagetype").select2("val") == "" || $(this).parents(".modal-content").find(".modal-body #addobject").select2("val") == "" || $(this).parents(".modal-content").find(".modal-body #addlinkto").select2("val") == "" || $(this).parents(".modal-content").find(".modal-body #addpriority").select2("val") == "" || $(this).parents(".modal-content").find(".modal-body #addstatus").select2("val") == ""){
                        alert("Please fill all the required fields");
                        return;
                    }
                    
                    //self.button('loading');
                    saveobj.assigned_to = $(this).parents(".modal-content").find(".modal-body #addto").select2("val");
                    saveobj.Message_type = $(this).parents(".modal-content").find(".modal-body #addmessagetype").select2("val");
                    saveobj.obj_type = $(this).parents(".modal-content").find(".modal-body #addobject").select2("val");
                    saveobj.linkto = $(this).parents(".modal-content").find(".modal-body #addlinkto").select2("val");
                    saveobj.priority = $(this).parents(".modal-content").find(".modal-body #addpriority").select2("val");
                    saveobj.Status = $(this).parents(".modal-content").find(".modal-body #addstatus").select2("val");
                    saveobj.content = $(this).parents(".modal-content").find(".modal-body .Editor-editor").html();
                    saveobj.username = portaluser;
                    saveobj.action="savedata";
                    $(this).parents("#addmessagemodal").data("process",true);
                    fileuploader.update({dynamicFormData: function()
                    {
                        var data ={"stetas":true,messagedata:saveobj};
                        return data;        
                    }});
                    $('#addmessagemodal').modal('hide');
                    fileuploader.startUpload();
                })
                $('#addmessagemodal').on('hidden.bs.modal', function (event) {
                    $("#savebtn").button('reset');
                    if(!$(this).data("process"))
                       fileuploader.cancelAll(); 
                });
                
                $('#addmessagemodal').on("show.bs.modal",function(event){
                    var target = $(event.relatedTarget);
                    var modal = $(this);
                    modal.data("process",false);
                    listsetintervel = setInterval(function(){
                        if(listdata != null)
                           addmessage();
                    },500);
                     function addmessage(){
                         clearInterval(listsetintervel);
                         modal.find(".modal-body").find("form").html(addMessageUI(listdata));
                         modal.find(".modal-body").find("form .select2").select2({ dropdownParent: $('.modal'),placeholder : 'Please Select' });
                         //$("#addmessagecontent").Editor(testexitremoveopt);
                         modal.find(".modal-body").find("#addobject").select2({ placeholder : 'Please Select' }).on("change",function(event){
                             var objvalue = $(this).val();
                             var getlinkval = listdata.object_value[objvalue];
                             var opts = "";
                             $.each(getlinkval,function(index,value){
                                 opts +="<option value='"+index+"'>"+value+"</option>";
                             });
                             $('#addlinkto').select2('enable');
                             $('#addlinkto').html("");
                             $('#addlinkto').append(opts);
                             $(this).parents('.form-group').next().find("label").html($(this).find("option:selected").text());
                         });
                     }
                });

                $('#addbtn').on("click",function(){
                    clearajaxcalls(allajaxcalls);
                    $('.overlay').show("fast");
                    $('#addfoulder').modal('hide');
                    var deletedlink = '<?php echo $addfolder_url ?>'+$('.nav-title a').last().data('id')+"/"+$('#newfoldername').val();
                    ajaxcall(deletedlink,null,"get",function(data){
                        $('#newfoldername').val("New folder");
                         loaddrive(rootfoulder,progressid);
                    },ajaxdefaulterrorcallback);
                });
                //logs
                $('#myModal').on("show.bs.modal",function(event){
                    var modal = $(this);
                    modal.find(".modal-body").html('<iframe id="logdata" src="logdetails.php?email=<?php echo $row['notes']; ?>&user=<?php echo $portal_user; ?>" style="border:0; width:100%; min-height:380px;"></iframe>')
                });
                //getoptdata();
                function getoptdata(){
                   ajaxcall("msg_list_ajax.php",{action:'listdata'},"post",function(data){
                       listdata = $.parseJSON(data);
                       $("#loader2").hide();
                   },ajaxdefaulterrorcallback);
               }
                 
                 function addMessageUI(listdata){
                    var messagespace = "";
                    var objecttype = "";
                    var objectval = '<div class="form-group">\n\
                                        <label class="col-lg-3 control-label">Linked To<span style="color:red;"> *</span></label>\n\
                                        <div class="col-lg-9">\n\
                                            <select id="addlinkto" class="select2">\n\
                                                <option></option>\n\
                                            </select>\n\
                                        </div>\n\
                                    </div>';
                    var states = "";
                    var assianto = "";
                    var priority = "";
                    var content = '<div class="form-group">\n\
                                        <div class="col-lg-12">\n\
                                            <div id="addmessagecontent" contenteditable="true" placeholder="Enter Message hear..." draggable="false"></div>\n\
                                        </div>\n\
                                    </div>';

                    $.each(listdata,function(index,value){
                        
                        if(index == "message_type"){
                            messagespace += '<div class="form-group">\n\
                                        <label class="col-lg-3 control-label">Message Type<span style="color:red;"> *</span></label>\n\
                                        <div class="col-lg-9">\n\
                                            <select id="addmessagetype" class="select2"><option></option>'
                                                $.each(value,function(i,v){
                                                   messagespace += "<option value='"+i+"'>"+v+"</option>";
                                                });
                            messagespace += '</select>\n\
                                        </div>\n\
                                    </div>'
                        }

                        if(index == "object_type"){
                            objecttype += '<div class="form-group">\n\
                                        <label class="col-lg-3 control-label">Object Type<span style="color:red;"> *</span></label>\n\
                                        <div class="col-lg-9">\n\
                                            <select id="addobject" class="select2"><option></option>'
                                                $.each(value,function(i,v){
                                                   /*if(i == interest) 
                                                      objecttype += "<option value='"+i+"' selected>"+v+"</option>";
                                                   else*/
                                                      objecttype += "<option value='"+i+"'>"+v+"</option>"; 

                                                });
                            objecttype += '</select>\n\
                                        </div>\n\
                                    </div>'
                        }

                        if(index == "status"){
                            states += '<div class="form-group">\n\
                                        <label class="col-lg-3 control-label">Status<span style="color:red;"> *</span></label>\n\
                                        <div class="col-lg-9">\n\
                                            <select id="addstatus" class="select2"><option></option>'
                                                $.each(value,function(i,v){
                                                   states += "<option value='"+i+"'>"+v+"</option>";

                                                });
                            states += '</select>\n\
                                        </div>\n\
                                    </div>'
                        }

                        if(index == "priority"){
                            priority += '<div class="form-group">\n\
                                        <label class="col-lg-3 control-label">Priority<span style="color:red;"> *</span></label>\n\
                                        <div class="col-lg-9">\n\
                                            <select id="addpriority" class="select2"><option></option>'
                                                $.each(value,function(i,v){
                                                   priority += "<option value='"+i+"'>"+v+"</option>";

                                                });
                            priority += '</select>\n\
                                        </div>\n\
                                    </div>'
                        }

                        if(index == "assigned_to"){
                            assianto += '<div class="form-group">\n\
                                        <label class="col-lg-3 control-label">To<span style="color:red;"> *</span></label>\n\
                                        <div class="col-lg-9">\n\
                                            <select id="addto" class="select2"><option></option>'
                                                $.each(value.users,function(i,v){
                                                   assianto += "<option value='"+i+"'>"+v+"</option>";
                                                });
                                               assianto +='<optgroup label="User Group"></optgroup>';

                                                $.each(value.user_group,function(i,v){
                                                   assianto += "<option value='"+i+"'>"+v+"</option>";
                                                });
                            assianto += '</select>\n\
                                        </div>\n\
                                    </div>'
                        }


                    });
                    var html = (messagespace + objecttype + objectval + states + assianto + priority + content);
                    return html;
                 }
                 
                $('#movebtn').on("click",function(){
                    clearajaxcalls(allajaxcalls);
                    $('.overlay').show("fast");
                    $('#movefile').modal('hide');
                    var deletedlink = '<?php echo $move_url ?>'+targetfid+'<?php echo $_SESSION['authUser']; ?>';
                    ajaxcall(deletedlink,null,"get",function(data){
                        filemovie = true;
                         loaddrive(rootfoulder,progressid);
                    },ajaxdefaulterrorcallback);
                });
                $('.ajax-filelist').on("contextmenu", function (e) {
                  return false;
                });
                $('.clearinput').click(function(){
                    $(this).parent().find("input").val("");
                    $('.ajax-filelist .entry').hide();
                    $('.ajax-filelist .entry').filter(function(){
                        var patt1 = new RegExp($("#searchtype").val(),"i");
                        var res1 = patt1.test($(this).data('type'));
                        var filtertype = null;
                        if($("#searchtype").val() != "all")
                            filtertype = res1;
                        else
                           filtertype =  true;
                        
                        return filtertype;
                    }).not('.parentfolder,.newfolder').show();
                    //if(inputval =="")
                      $('.parentfolder,.newfolder').show();
                    
                })
                $("#searchentery").on("keyup",function(){
                    var inputval = $(this).val().trim();
                    $('.ajax-filelist .entry').hide();
                    $('.ajax-filelist .entry').filter(function(){
                        var patt = new RegExp(inputval,"i");
                        var patt1 = new RegExp($("#searchtype").val(),"i");
                        var res = patt.test($(this).data('name'));
                        var res1 = patt1.test($(this).data('type'));
                        var filtertype = null;
                        if($("#searchtype").val() != "all")
                            filtertype = (res==true && res1 == true);
                        else
                           filtertype =  res;
                        return filtertype;
                    }).not('.parentfolder,.newfolder').show();
                    //if(inputval =="")
                      $('.parentfolder,.newfolder').show();
                });
                
                $(document).on("change","#searchtype",function(){
                    var inputval = $("#searchentery").val().trim();
                    $('.ajax-filelist .entry').hide();
                    $('.ajax-filelist .entry').filter(function(){
                        var patt = new RegExp(inputval,"i");
                        var patt1 = new RegExp($("#searchtype").val(),"i");
                        var res = patt.test($(this).data('name'));
                        var res1 = patt1.test($(this).data('type'));
                        var filtertype = null;
                        if($("#searchtype").val() != "all"){
                            filtertype = (res==true && res1 == true);
                        }else{
                           filtertype =  res;
                        }
                        
                        return filtertype;
                    }).not('.parentfolder,.newfolder').show();
                    //if(inputval =="")
                      $('.parentfolder,.newfolder').show();
                  
                });
                
                $(document).on("click",".file,#previewfile",function(){
                    $('a.show-pop-dropdown').webuiPopover('hide');                
                    if($(this).parent().is('li') == true)
                    {
                       url = fileshowurl($(this).parent(),viewmimetypesfile,viewmimetypesdocs)
                    }
                    else
                    {
                       url = fileshowurl($(this),viewmimetypesfile,viewmimetypesdocs)
                    }
                  // alert($(this).data("type"));
                  //alert(url);
                     if(islightboxopen){
                        var lightbox = lity();
                        lightbox(url);
                        $(document).on('click', '[data-lightbox]', lightbox);
                        $('.lity').hide();
                        $('.overlay').show();
                      }
               });
               function fileshowurl(element,mimetype1,mimetype2){
                    var getfiletype = element.data('type');
                        if($.inArray(getfiletype,mimetype1) !=-1){
                            islightboxopen = true;
                           return  "../../patient_file/summary/curlfileview.php?url="+'<?php echo $fileview_url ?>'+element.data('id');
                        }else if($.inArray(getfiletype,mimetype2) != -1){
                            islightboxopen = true;
                            var url = "";
                            currentfileid = element.data('id');
                            //alert(mimetype2[$.inArray(getfiletype,mimetype2)])
                           switch(mimetype2[$.inArray(getfiletype,mimetype2)]){
                                case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
                                case "text/csv":
                                  url = '<?php echo $viewfile; ?>'+element.data('id');
                                break;
                                case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
                                case "application/msword": 
                                case "application/rar": 
                                case "application/zip":
                                case "application/ogg":
                                case "image/x-photoshop":
                                case "application/postscript":
                                 url = '<?php echo $viewfile; ?>'+element.data('id');
                                break;
                                case "application/vnd.google-apps.spreadsheet":
                                  url = '<?php echo $viewfile; ?>'+element.data('id')+'&ggl=1';
                                break;
                                case "application/vnd.google-apps.document":
                                  url = '<?php echo $viewfile; ?>'+element.data('id')+'&ggl=1';
                                break;
                                default:
                                    url = "../../patient_file/summary/curlfileview.php?url="+'<?php echo $fileview_url ?>'+element.data('id');

                            }
                           return  url;
                        }else{
                            islightboxopen = false;
                            var url = '<?php echo $filedownload_url; ?>'+element.data('id')+"&type="+element.data('type')+"&name="+element.data('name');
                             $('body').append("<iframe src='"+url+"' frameborder='0' onload='downloadframelc(this)'></iframe>");
                             //alert(url);
                           return "";
                        }
                   }
                   
                   if($(window).width() > 767)
                        $("#wrapper").addClass("active");        

                   $("#menu-toggle").click(function(e) {
                       e.preventDefault();
                       $("#wrapper").toggleClass("active");
                   });
                });

            function DoPost(page_name, provider,refer) {
                    method = "post"; // Set method to post by default if not specified.

                  // alert(provider);

                    var form = document.createElement("form");
                    form.setAttribute("method", method);
                    form.setAttribute("action", page_name);
                    var key='provider';
            //                for(var key in params) {
            //                    if(params.hasOwnProperty(key)) {
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", key);
                    hiddenField.setAttribute("value", provider);
                    var key1='refer';
                    var hiddenField1 = document.createElement("input");
                    hiddenField1.setAttribute("type", "hidden");
                    hiddenField1.setAttribute("name", key1);
                    hiddenField1.setAttribute("value", refer);
                    form.appendChild(hiddenField1);
                    form.appendChild(hiddenField);
                    document.body.appendChild(form);
                    form.submit();
            }
            function frameload(){
                $('.overlay').hide();
                $('.lity').show();
                //alert($('.lity iframe').contents().find("body>img").height() + " : " + $('.lity iframe').height())
                unsharelink = '<?php echo $unsharefile; ?>'+currentfileid + "/anyoneWithLink";
                try{
                     $('.lity iframe').contents().find("body>img").parents("body").css({"text-align":"center"});
                     if($('.lity iframe').contents().find("body>img").height() > $('.lity iframe').height())
                       $('.lity iframe').contents().find("body>img").css({height:"100%"});
                     else
                       $('.lity iframe').contents().find("body>img").css({position:"absolute",bottom:0,left:0,right:0,top:0,margin:"auto"});
               }catch(e){

               }
             }
            function downloadframelc(target){
                 setTimeout(function(){target.remove()},3000);
            }
            function unShareFile() {
                if(window.currentfileid)
                    $.ajax({url:unsharelink,type:"get",xhrFields: {withCredentials: true},data:null, crossDomain: true,error:function(e){console.log(e)},success:function(data){console.log(data);window.currentfileid = ""}});
            }
            function imgError(image) {
                //image.onerror = "";
                if($(image).data("thumerror") == undefined){
                     $(image).data("thumerror","error");
                    image.src = "https://drive.google.com/thumbnail?id="+$(image).parents('.entry').data('id');
                }else{
                    image.src = "../../patient_file/summary/driveassets/driveimages/defaultfile.png";
                }
                return true;
            }
            if(window.closed){
                alert("closed");
            }
            function getMobileOperatingSystem() {
                var userAgent = navigator.userAgent || navigator.vendor || window.opera;

                    // Windows Phone must come first because its UA also contains "Android"
                  if (/windows phone/i.test(userAgent)) {
                      return "Windows Phone";
                  }

                  if (/android/i.test(userAgent)) {
                      return "Android";
                  }

                  // iOS detection from: http://stackoverflow.com/a/9039885/177710
                  if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                      return "iOS";
                  }

                  return "unknown";
            }
            /*$(window).bind("beforeunload", function(event) {
                    unShareFile();
                    return  confirm("Do you really want to close?"); 
             });*/
        </script>
</head>
<body onunload="unShareFile()">
    <div id="wrapper" class="">
        <div id="sidebar-wrapper">
            <ul id="sidebar_menu" class="sidebar-nav">
               <li class="sidebar-brand"><a id="menu-toggle" href="#">Menu<span id="main_icon" class="glyphicon glyphicon-align-justify"></span></a></li>
            </ul>
            <ul class="sidebar-nav" id="sidebar">
              <input type="hidden" id="sidenavep" value="<?php echo $page_id; ?>"/>
            </ul>
        </div>
        <div id="page-content-wrapper">
             <div class="page-content inset">
                 <div class= "row">
                     <div class="col-md-12">
                         <div id="content">
                             <div class="list-container uyd-grid">
                                 <div class="nav-header">
                                     <a href="#" class="sideicon" id="sideicon"></a>
                                     <a class="nav-home" href="javascript:void(0)" title="Back to our first folder"><span class="glyphicon glyphicon-home pull-left"></span></a>
                                     <a href="javascript:void(0)" class="nav-refresh" title="Refresh"><span class="glyphicon glyphicon-refresh"  ></span></a>
                                     <a href="javascript:void(0)" class="nav-search"  data-placement="bottom-left"><span class="glyphicon glyphicon-search" ></span></i></a>
                                     <div  class="webui-popover-content">
                                         <ul class="dropdown-menu" >
                                             <li class="searchdrive">
                                                 <div class="input-group inner-addon left-addon">
                                                     
                                                     <span class="input-group-addon">
                                                         <select class="selectpicker" data-style="btn-primary" id="searchtype">
                                                            <option value="all">All</option>                                                            
                                                            <option value="vnd.google-apps.folder">Folder</option>
                                                            <option value="image/">Image</option>
                                                            <option value="application/pdf">Pdf</option>
                                                        </select>
                                                     </span>                                  <i class="left-a glyphicon glyphicon-search"></i>
                                                     <input id="searchentery" type="text" class="form-control" maxlength="50" placeholder="Search filenames" /><span class="input-group-addon clearinput"><i class="glyphicon glyphicon-remove-sign"></i> </span>
                                                 </div>
                                             </li>
                                         </ul>
                                     </div>
                                     <?php if(in_array('logs',$avail3)){ ?>
                                     <a href="javascript:void(0)" style="float:right;" data-toggle="modal" data-target="#myModal">Logs</a>
                                     <?php } ?>
                                     <div class="nav-title"></div>
                                 </div>
                                 <div class="ajax-filelist">
                                     <div class="overlay">
                                         <div class="loader-contenar">
                                             <div class="drive"><img src="../../patient_file/summary/driveassets/driveimages/drive.png" width="83px"/></div>
                                             <div class="circle"></div>
                                         </div>
                                         <div class="reload"><a href="javascript:void(0)"><img width="100px" src="../../patient_file/summary/driveassets/driveimages/reload.png"></a>
                                         </div>
                                     </div>    
                                     <div class="files layout-grid">
                                     </div>
                                 </div>
                                 <div class="fileupload-container" style="width:100%;max-width:100%">
                                     <div id="fileupload" class="fileuploadform">
                                         <div class="fileupload-drag-drop">
                                             <div>
                                               <img width="190" height="98" src="../../patient_file/summary/driveassets/driveimages/drag-upload.png">
                                               <p>Drag your files here ...</p>
                                             </div>
                                         </div>
                                         <div id="mulitplefileuploader" data-formurl="teststring">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                      <!-- Modal for Rename-->
                     <div class="modal fade" name = "rename" id="rename" tabindex="-1" role="dialog" aria-hidden="true">
                         <div class="vertical-alignment-helper">
                             <div class="modal-dialog vertical-align-center modal-sm">
                                 <div class="modal-content">
                                     <div class="modal-header">
                                         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                                          <h4 class="modal-title" id="myModalLabel">Rename</h4>
                                     </div>
                                     <div class="modal-body">
                                         <div>Rename to:</div>
                                         <div><input type="text" id="renamebox"  class="form-control input-sm"/></div>
                                     </div>
                                     <div class="modal-footer">
                                         <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                         <button type="button" class="btn btn-primary" id="renamebtn">Rename</button>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <!-- Modal for Delete-->
                     <div class="modal fade" name = "delete" id="delete" tabindex="-1" role="dialog" aria-hidden="true">
                         <div class="vertical-alignment-helper">
                             <div class="modal-dialog vertical-align-center modal-sm">
                                 <div class="modal-content">
                                     <div class="modal-header">
                                         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                                          <h4 class="modal-title" id="myModalLabel">Delete</h4>
                                     </div>
                                     <div class="modal-body">
                                         <div id="deletemessage">Do you really want to delete: <em></em></div>
                                     </div>
                                     <div class="modal-footer">
                                         <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                         <button type="button" class="btn btn-primary" id="deletebtn">Delete</button>
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
                                          <h4 class="modal-title" id="myModalLabel">Add folder</h4>
                                     </div>
                                     <div class="modal-body">
                                         <div><input type="text" id="newfoldername"  class="form-control input-sm" value="New folder"/></div>
                                     </div>
                                     <div class="modal-footer">
                                         <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                         <button type="button" class="btn btn-primary" id="addbtn">Add folder</button>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>

                     <!-- Logs -->
                     <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                         <div class="modal-dialog modal-lg" role="document">
                           <div class="modal-content">
                             <div class="modal-header">
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                               <h4 class="modal-title" id="myModalLabel">Log Details</h4>
                             </div>
                             <div class="modal-body">

                             </div>
                             <div class="modal-footer">
                               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                             </div>
                           </div>
                         </div>
                       </div>

                     <!-- Modal -->
                     <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="addmessagemodal" data-backdrop="static" data-keyboard="false" class="modal fade" style="display: none;">
                         <div class="modal-dialog">
                             <div class="modal-content">
                                 <div class="modal-header">
                                     <button aria-hidden="true" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                                     <h4 class="modal-title">Add New Message</h4>
                                 </div>
                                 <div class="modal-body">
                                     <form role="form" class="form-horizontal">
                                         <div id="loader">
                                              <div class="ajax-spinner-bars">
                                                  <div class="bar-1"></div>
                                                  <div class="bar-2"></div>
                                                  <div class="bar-3"></div>
                                                  <div class="bar-4"></div>
                                                  <div class="bar-5"></div>
                                                  <div class="bar-6"></div>
                                                  <div class="bar-7"></div>
                                                  <div class="bar-8"></div>
                                                  <div class="bar-9"></div>
                                                  <div class="bar-10"></div>
                                                  <div class="bar-11"></div>
                                                  <div class="bar-12"></div>
                                                  <div class="bar-13"></div>
                                                  <div class="bar-14"></div>
                                                  <div class="bar-15"></div>
                                                  <div class="bar-16"></div>
                                              </div>
                                              <div id="loadertitle">Data Loading...</div>
                                          </div>
                                     </form>
                                 </div>
                                 <div class="modal-footer" style="padding:5px 15px;">
                                      <button type="button" class="btn btn-send btn-sm" id="savebtn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Sending Message">Send&upload</button>
                                 </div>
                             </div><!-- /.modal-content -->
                         </div><!-- /.modal-dialog -->
                     </div><!-- /.modal -->
                 </div>
             </div>
         </div>
    </div>
    <script src="../../patient_file/summary/driveassets/js/jquery.uploadfile.js"></script>
</body>
</html>
  