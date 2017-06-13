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

$pagename = "documents";
$subpage = "Drive View";
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');


$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';
$portal_user=$_REQUEST['provider'] ? $_REQUEST['provider'] : $_SESSION['portal_username'];

$parent = $_REQUEST['parent']; 
$folder = $_REQUEST['folder']; 
$file_id=$_REQUEST['file_id'];
$pid=$_REQUEST['pid'];
$getcategory = $_REQUEST['category'];
$action = $_REQUEST['action']; 
$category_arr= array( 
        array('title' =>'patients','fieldid'=>'parent_folder'),
        array('title' =>'users','fieldid'=>'user_parent_folder'),
        array('title' =>'insurance','fieldid'=>'ins_parent_folder'),
        array('title' =>'pharmacy','fieldid'=>'pharmacy_parent_folder'),
        array('title' =>'agency','fieldid'=>'addrbk_parent_folder'),
        array('title' =>'facility','fieldid'=>'facility_parent_folder'),
        array('title' =>'attachments','fieldid'=>'email_parent_folder')
);

 $mime_types= array(
        "xls" =>'application/vnd.ms-excel',
        'application/pdf'=> "../../../images/pdf-icon.png" ,
        'application/vnd.google-apps.folder'=>'../../../images/folder.jpg'
    );
// to get configured email
$sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
$row = sqlFetchArray($sql);
    //Drive access
   $sql_vis=sqlStatement("SELECT drive_access from tbl_user_custom_attr_1to1 where userid='".$_SESSION['authId']."'");
   $row1_vis=sqlFetchArray($sql_vis);
   $avail3 = [];
   if(!empty($row1_vis)) { 
       $avail3=explode("|",$row1_vis['drive_access']);
   }
//parent folder 
$selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "'");
$sel_rows = sqlFetchArray($selection);
foreach ($category_arr as $key => $item) {
    if($getcategory == $item['title']){
        $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows[$item['fieldid']]);
    }
}
if($action == "view"){
    $parentid = $folder;
}
/*if($_REQUEST['folder']==''){
    $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['parent_folder']);
}else{
    $parentid =$_REQUEST['folder'];
}*/
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://';
$_GLOBALS["parentid"] = $parentid;
$foulderdata = $protocol . $_SERVER['HTTP_HOST'] . '/api/DriveSync/listallfilespagetoken_web/' . $row['notes'];
$foulderinfo =  $protocol . $_SERVER['HTTP_HOST'] . '/api/DriveSync/getfileinfo_web/' . $row['notes'];  
$rename_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/renameFile_web/emr/'.$_SESSION['portal_username'].'/';
$delete_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/deletefile_web/'.$row['notes'].'/emr/'.$_SESSION['portal_username'].'/';
$addfolder_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes']."/";
$move_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/movefile_web/'.$row['notes'].'/'.trim($file_id," ").'/';
$fileview_url=$protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/downloadfile_web/'.$row['notes'].'/';
 $fileupload_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/uploadfile_web/'.$row['notes'].'/emr/'.$_SESSION['portal_username'].'/';
$filedownload_url = $protocol.$_SERVER['HTTP_HOST'].'/interface/main/allcarereports/download_file.php?category='.$getcategory.'&file_id=';
$viewfile=$protocol.$_SERVER['HTTP_HOST'].'/interface/main/allcarereports/view_file.php?file_id=';
$unsharefile = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/delete_permission/'.$row['notes'].'/';
$comentsurl = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/addcomment/'.$row['notes'].'/';
?>
<html> 
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="../drive_view/driveassets/css/jquery.webui-popover.min.css">
        <link rel="stylesheet" href="../drive_view/driveassets/css/lity.css">
        <link href="../drive_view/driveassets/css/uploadfile.css" rel="stylesheet"> 
	<link rel="stylesheet" href="../drive_view/driveassets/drive.css">
        <link rel="stylesheet" href="../drive_view/driveassets/drivecustom.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script src="../drive_view/driveassets/js/jquery.webui-popover.min.js"></script>
        <script src="../drive_view/driveassets/js/lity.js"></script>
        <script src="../drive_view/driveassets/js/jquery.uploadfile.js"></script>
        <script>
            var rootfoulder = '<?php echo $parentid ?>';
            var driveopt = ('<?php echo $row1_vis['drive_access'] ?>').split("|");
            var parentid = "";
            var progressid = "";
            var targetfid = "";
            var targetfidname = "";
            var timeout = null;
            var filemovie = '<?php echo $action ?>' == "view"?true:false;
            var files=["foulder_icon.png","fileloader.gif","foulder_add_icon.png","drive.png","reload.png","defaultfile.png","File-Audio-icon.png","zip-con.png","xml-icon.png","drag-upload.png"]
            var currentajaxcall = null;
            var allajaxcalls = [];
            var viewmimetypesfile = ["text/plain","text/html","application/x-httpd-php","image/jpeg","image/png","application/pdf","image/gif","image/bmp"]
            var viewmimetypesdocs = ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/vnd.google-apps.document","application/vnd.google-apps.spreadsheet","application/msword","text/csv","application/rar","application/zip","application/ogg","image/x-photoshop","application/postscript"];
            var islightboxopen = true;
            var currentfileid = "",unsharelink="";
            var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
            var allfilesdata = [];
            
            loadimages(files);
            function loadimages(files){
                var filelength = files.length;
                 for (var i = 0; i < filelength; i++) 
                 {
                    var theImage = new Image();
                    theImage.src = "../drive_view/driveassets/driveimages/" +files[i];
                 }
            }
           $(function(){
               var barwidth = "100%";
               if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1){
                    barwidth = "86%";
               }
                var settings = {
                url: '<?php echo $fileupload_url; ?>'+rootfoulder+"/"+'<?php echo $getcategory; ?>',
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
            var fileuploader = '<?php echo $action ?>' != "view"?$("#mulitplefileuploader").uploadFile(settings):"";
            if('<?php echo $action ?>' == "view")
                $('.fileupload-container').hide();$('.list-container').css({margin:10});
                
               $('a.nav-search').webuiPopover('destroy').webuiPopover({trigger:'click',padding:0,width:215});	
               function ajaxcall(url,data,type,callback,errorcallback){
                  currentajaxcall = $.ajax({url:url,type:type,xhrFields: {withCredentials: true},data:data, crossDomain: true,error:errorcallback,success:callback});
                  allajaxcalls.push(currentajaxcall);
               }
              loaddrive(rootfoulder,rootfoulder);
              function loaddrive(rootfoulder,folderid){
                progressid = folderid;
                var folderinfourl = '<?php echo $foulderinfo?>'+"/"+folderid;
                var gerfilesurl = '<?php echo $foulderdata?>'+"/"+folderid+"/all";
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
                        ajaxcall(getparentdataurl,null,"get",function(parentdata){
                            ajaxcall(gerfilesurl+"/empty",null,"get",getfiles,ajaxdefaulterrorcallback);
                            var parentdata = $.parseJSON(parentdata);
                            $('.nav-title').html('<a href="javascript:void(0)" class="folder" data-id="'+parentdata["id"]+'">'+parentdata["name"]+'</a> &lsaquo;&lsaquo; '+
                                    '<a href="javascript:void(0)" class="folder" data-id="'+folderinfodata["id"]+'"><strong>'+folderinfodata["name"]+'</strong></a>');
                        },ajaxdefaulterrorcallback);
                    }else{
                        ajaxcall(gerfilesurl+"/empty",null,"get",getfiles,ajaxdefaulterrorcallback);
                        $('.nav-title').html('<a href="javascript:void(0)" class="folder" data-id="'+folderinfodata["id"]+'"><strong>'+folderinfodata["name"]+'</strong></a>');
                    }
                 }
                 function getfiles(data){
                     //console.log( "foulderfiles: " + data);
                     var getfileinfo = '<?php echo $foulderinfo?>';
                     var files = $.parseJSON(data);
                     if(files[0] != undefined){
                        if(files[0].error_code == "403"){
                            var fieldurl = this.url == undefined?gerfilesurl+"/empty":this.url;
                            setTimeout(ajaxcall(fieldurl,null,"get",getfiles,ajaxdefaulterrorcallback),3000);
                            return;
                        }
                    }
                    var pagetocken = files.nextpageToken;
                    delete files.nextpageToken;
                    allfilesdata.push(files);
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
                     if($.inArray( "create_folder", driveopt ) !=-1)
                        files.unshift({name:"Add folder",id:"",mimeType:"application/addfoulder",thumbnail:"../drive_view/driveassets/driveimages/foulder_add_icon.png",opt:false});
                     if(rootfoulder !=folderid)
                        files.unshift({name:"Previous folder (My Drive)",id:parentid,mimeType:"application/pre",thumbnail:"../drive_view/driveassets/driveimages/foulder_icon.png",opt:false});
                    
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
                        }else if(value == "comment"){
                             fileopt.push("<a href='javascript:void(0)' data-toggle='modal' data-target='#comment'><span class='glyphicon glyphicon-comment' aria-hidden='true'></span>"+value.charAt(0).toUpperCase()+value.slice(1)+"</a>");
                        }
                    });
                   addCustomThumbnail(files);
                /* code for create entery html*/
                    var html = "";
                     $.each(files,function(index,value){
                        var opt = getfiletype(value['mimeType'])=="file"?fileopt:folderopt;                       
                              html += '<div class="entry '+getfiletype(value['mimeType'])+'" data-id="'+value['id']+'" data-type="'+value['mimeType']+'" data-name="'+value['name']+'">\n\
                                            <div class="entry_block" style="width: 131px;">';
                              if(getfiletype(value['mimeType']) != "parentfolder" && getfiletype(value['mimeType']) != "newfolder"){
                                  html +='<div class="entry_edit"><a class="entry_edit_menu show-pop-dropdown" data-placement="bottom-left">\n\
                                            <i class="glyphicon glyphicon-menu-down"></i></a>';
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
                     $('.newfolder').insertAfter($(".layout-grid .folder").last())
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
                    '<?php echo $action ?>' != "view"?fileuploader.update({url:'<?php echo $fileupload_url; ?>'+progressid+"/"+'<?php echo $getcategory; ?>'}):"";
                    //$('.fileupload-drag-drop').show();
                    //fileuploader.reset();
                 }
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
                $.each($.makeArray(data),function(index,value){
                    if((value.mimeType).toLowerCase()=="application/wav" || (value.mimeType).toLowerCase()=="application/aac" || (value.mimeType).toLowerCase()=="application/mp3" || (value.mimeType).toLowerCase()=="application/mpeg" || (value.mimeType).toLowerCase()=="application/ogg" || (value.mimeType).toLowerCase()=="application/wav" || (value.mimeType).toLowerCase()=="application/webm" || (value.mimeType).toLowerCase()=="application/aiff" )
                    {
                       value.thumbnail = "../drive_view/driveassets/driveimages/File-Audio-icon.png";

                    }if((value.mimeType).toLowerCase()=="application/zip")
                    {
                       value.thumbnail = "../drive_view/driveassets/driveimages/zip-con.png";

                    }if((value.mimeType).toLowerCase()=="application/rar")
                    {
                       value.thumbnail = "../drive_view/driveassets/driveimages/rar-con.png";

                    }if((value.mimeType).toLowerCase()=="text/xml")
                    {
                       value.thumbnail = "../drive_view/driveassets/driveimages/xml-icon.png";

                    }else if ((value.mimeType).toLowerCase() == "application/vnd.google-apps.folder")
                    {
                        value.thumbnail = "../drive_view/driveassets/driveimages/foulder_icon.png"
                        
                    }else if(value.thumbnail == null){
                        value.thumbnail = "https://drive.google.com/thumbnail?id=" + value.id;
                    }
                })

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
                console.clear();
                callslist = []
            }
             
            $('#rename,#delete,#movefile,#comment').on("show.bs.modal",function(event){
                $('a.show-pop-dropdown').webuiPopover('hide');
                var target = $(event.relatedTarget);
                var correntname = target.parent('li').data('name');
                targetfid =  target.parent('li').data('id');
                targetfidname = correntname;
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
                var renamelink = '<?php echo $rename_url ?>'+'<?php echo $getcategory; ?>'+"/"+targetfid+"/"+$('#renamebox').val()+"/"+'<?php echo $row['notes'] ?>'+'/'+progressid;
                ajaxcall(renamelink,null,"get",function(data){
                     loaddrive(rootfoulder,progressid);
                },ajaxdefaulterrorcallback);
            })
             
            $('#deletebtn').on("click",function(){
                clearajaxcalls(allajaxcalls);
                $('.overlay').show("fast");
                $('#delete').modal('hide');
                var deletedlink = '<?php echo $delete_url ?>'+'<?php echo $getcategory; ?>'+"/"+targetfid+'/'+progressid;
                ajaxcall(deletedlink,null,"get",function(data){
                     loaddrive(rootfoulder,progressid);
                },ajaxdefaulterrorcallback);
            });
            $(document).on("click",'.downloadfile',function(){
                var filename = $(this).parent().data("name");
                var id = $(this).parent().data("id");
                var type = $(this).parent().data("type");
                url = '<?php echo $filedownload_url; ?>'+id+"&type="+type+"&name="+filename;
                $('body').append("<iframe src='"+url+"' frameborder='0' onload='downloadframelc(this)'></iframe>");
            });
            
            
            $(document).on("click",".newfolder",function(){
                $('a.show-pop-dropdown').webuiPopover('hide');
                $('#addfoulder').modal('show');
            })
             
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
            
             $('#addcomment').on("click",function(){
                //clearajaxcalls(allajaxcalls);
                $('.overlay').show("fast");
                $('#addfoulder').modal('hide');
               var commentlink = '<?php echo $comentsurl ?>'+targetfid+"/"+ Base64.encode($('#commentarea').val());
                ajaxcall(commentlink,null,"get",function(data){
                    $('#commentarea').val("");
                    $('.overlay').hide("fast");
                },ajaxdefaulterrorcallback);
            });
            

            $('#movebtn').on("click",function(){
                clearajaxcalls(allajaxcalls);
                $('.overlay').show("fast");
                $('#movefile').modal('hide');
                var deletedlink = '<?php echo $move_url ?>'+targetfid+'/'+'<?php echo $_SESSION['portal_username']; ?>'+'/<?php echo $_REQUEST['page_name']; ?>/'+targetfidname.replace(" ","_");
                console.log(deletedlink);
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
                 $('.ajax-filelist .entry').show();
            })
            $("#searchentery").on("keyup",function(){
                var inputval = $(this).val().trim();
                $('.ajax-filelist .entry').hide();
                $('.ajax-filelist .entry').filter(function(){
                    var patt = new RegExp(inputval,"i");
                    var res = patt.test($(this).data('name'));
                    return res;
                }).not('.parentfolder,.newfolder').show();
                if(inputval =="")
                  $('.parentfolder,.newfolder').show();
            })
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
             if(islightboxopen){
                var lightbox = lity({ajaxurl:'<?php echo $unsharefile; ?>'+currentfileid + "/anyoneWithLink"});
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
                   return  "curlfileview.php?url="+'<?php echo $fileview_url ?>'+element.data('id');
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
                            url = "curlfileview.php?url="+'<?php echo $fileview_url ?>'+element.data('id');
                        
                    }
                   return  url;
                }else{
                    islightboxopen = false;
                    var url = '<?php echo $filedownload_url; ?>'+element.data('id')+"&type="+element.data('type')+"&name="+element.data('name');
                     $('body').append("<iframe src='"+url+"' frameborder='0' onload='downloadframelc(this)'></iframe>");
                   return "";
                }
           }
                             
        });
         
         function frameload(){
           $('.overlay').hide();
           $('.lity').show();
           //alert($('.lity iframe').contents().find("body>img").height() + " : " + $('.lity iframe').height())
           ///FILE_ID/anyoneWithLink';
           
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
                image.src = "../drive_view/driveassets/driveimages/defaultfile.png";
            }
            return true;
        }
        $(window).bind("beforeunload", function(event) {
                unShareFile();
                return  confirm("Do you really want to close?"); 
         }); 

        </script>
     </head>
     <body onunload="unShareFile()">   
       <!--<a href="javascript:void(0)" class="folder" ><strong>Start</strong></a>-->
        <div class="list-container uyd-grid">
            <div class="nav-header">
                <a class="nav-home" href="javascript:void(0)" title="Back to our first folder"><span class="glyphicon glyphicon-home pull-left"></span></a>
                <a href="javascript:void(0)" class="nav-refresh" title="Refresh"><span class="glyphicon glyphicon-refresh"  ></span></a>
                <a href="javascript:void(0)" class="nav-search"  data-placement="bottom-left"><span class="glyphicon glyphicon-search" ></span></i></a>
                <div  class="webui-popover-content">
                    <ul class="dropdown-menu" >
                        <li class="searchdrive">
                            <div class="input-group inner-addon left-addon">
                                <i class="left-a glyphicon glyphicon-search"></i>      
                                <input id="searchentery" type="text" class="form-control" placeholder="Search filenames" /><span class="input-group-addon clearinput"><i class="glyphicon glyphicon-remove-sign"></i> </span>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="nav-title"></div>
            </div>
            <div class="ajax-filelist">
                <div class="overlay">
                    <div class="loader-contenar">
                        <div class="drive"><img src="../drive_view/driveassets/driveimages/drive.png" width="83px"/></div>
                        <div class="circle"></div>
                    </div>
                    <div class="reload"><a href="javascript:void(0)"><img width="100px" src="../drive_view/driveassets/driveimages/reload.png"></a>
                    </div>
                </div>    
                <div class="files layout-grid">
                </div>
            </div>
            <div class="fileupload-container" style="width:100%;max-width:100%">
                <div id="fileupload" class="fileuploadform">
                    <div class="fileupload-drag-drop">
                        <div>
                          <img width="190" height="98" src="../drive_view/driveassets/driveimages/drag-upload.png">
                          <p>Drag your files here ...</p>
                        </div>
                    </div>
                    <div id="mulitplefileuploader" data-formurl="teststring">
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
        
        <!-- Modal for Delete-->
        <div class="modal fade" name = "delete" id="movefile" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="vertical-alignment-helper">
                <div class="modal-dialog vertical-align-center modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                             <h4 class="modal-title" id="myModalLabel">Move file</h4>
                        </div>
                        <div class="modal-body">
                            <div id="deletemessage">Do You want to move <b><?php echo $file_id; ?></b> file to this folder</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="movebtn">Move</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!--Modal for Comments-->
        
        <div class="modal fade" name = "comment" id="comment" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="vertical-alignment-helper">
                <div class="modal-dialog vertical-align-center modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                             <h4 class="modal-title" id="myModalLabel">Add Comment</h4>
                        </div>
                        <div class="modal-body">
                            <div><textarea placeholder="Enter Your Comment" id="commentarea" style="width: 100%;"></textarea></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="addcomment" data-dismiss="modal">Add Comment</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       </body>
</html>      