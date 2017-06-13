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
$pagename = "documents"; 



$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';
$portal_user=$_REQUEST['provider'] ? $_REQUEST['provider'] : $_SESSION['portal_username'];

$parent = $_REQUEST['parent']; 
$folder = $_REQUEST['folder']; 
$file_id=$_REQUEST['file_id'];
$pid=$_REQUEST['pid'];
$user_id=$_REQUEST['userid'];
$user_name=$_REQUEST['username'];
$category_arr= array(
        array('title' =>'patients','fieldid'=>'parent_folder'),
        array('title' =>'users','fieldid'=>'user_parent_folder'),
        array('title' =>'insurance','fieldid'=>'ins_parent_folder'),
        array('title' =>'pharmacy','fieldid'=>'pharmacy_parent_folder'),
        array('title' =>'address_Book','fieldid'=>'addrbk_parent_folder'),
        array('title' =>'facility','fieldid'=>'facility_parent_folder')
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
    $idvalue = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows[$item['fieldid']]);
    $category_arr[$key]['rootid'] = $idvalue;
}
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://';
$foulderdata = $protocol . $_SERVER['HTTP_HOST'] . '/api/DriveSync/listallfiles_web/' . $row['notes'];
$foulderinfo = $protocol . $_SERVER['HTTP_HOST'] . '/api/DriveSync/getfileinfo_web/' . $row['notes'];  
$rename_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/renameFile_web/';
$delete_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/deletefile_web/'.$row['notes'].'/';
$addfolder_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes']."/";
$move_url = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/movefile_web/'.$row['notes'].'/'.$file_id.'/';
$move_url = $protocol.$_SERVER['HTTP_HOST'].'/interface/patient_file/summary/link_folders_save.php?action=save&status=check&category='.$category.'&userid='.$user_id.'&folderid=';
$fileview_url=$protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/downloadfile_web/'.$row['notes'].'/';
//$parentlink_url = $protocol.$_SERVER['HTTP_HOST']."/interface/patient_file/summary/link_folders_save.php";
//$childunlink_url = $protocol.$_SERVER['HTTP_HOST']."/interface/patient_file/summary/unlink_folders_save.php";
?>
<link rel="stylesheet" href="driveassets/css/jquery.webui-popover.min.css">
<link rel="stylesheet" href="driveassets/css/lity.css">
<link rel="stylesheet" href="driveassets/drive.css">
<link rel="stylesheet" href="driveassets/css/linkfoulder.css">
<script src="driveassets/js/jquery.webui-popover.min.js"></script>
<script src="driveassets/js/lity.js"></script>
<script>
        var linkurl= "../helplinks.php";
        var rootfoulder = '';
        var driveopt = ('<?php echo $row1_vis['drive_access'] ?>').split("|");
        var parentid = "";
        var progressid = "";
        var targetfid = "";
        var timeout = null;
        var currentajaxcall = null;
        var userid = '<?php echo $user_id; ?>';
        var category = null;
        var linkedids = null; 
        var childunlinks = null;
        var files=["foulder_icon.png","fileloader.gif","foulder_add_icon.png","drive.png","reload.png","defaultfile.png","File-Audio-icon.png","zip-con.png","xml-icon.png","drag-upload.png"]
        var allajaxcalls = [];
        var islinked = false;
        var isrootfolder = false;
        var linkstates = "";
        loadimages(files);
        function loadimages(files){
            var filelength = files.length;
             for (var i = 0; i < filelength; i++) 
             {
                var theImage = new Image();
                theImage.src = "driveassets/driveimages/" +files[i];
             }
        }
       $(function(){
           $('a.nav-search').webuiPopover('destroy').webuiPopover({trigger:'click',padding:0,width:215});	
           function ajaxcall(url,data,type,callback,errorcallback,asynctype){
              var async = asynctype || true;
              currentajaxcall = $.ajax({url:url,async:async,type:type,xhrFields: {withCredentials: true},data:data, crossDomain: true,error:errorcallback,success:callback});
              allajaxcalls.push(currentajaxcall);
           }
          //loaddrive(rootfoulder,rootfoulder);
          function loaddrive(rootfoulder,folderid,category,userid){
            progressid = folderid;
            var folderinfourl = '<?php echo $foulderinfo?>'+"/"+folderid;
            var gerfilesurl = '<?php echo $foulderdata?>'+"/"+folderid+"/all";
//            ajaxcall('<?php echo $parentlink_url ?>',{category:category,userid:userid},"post",linkeddata,ajaxdefaulterrorcallback,false);
//            ajaxcall('<?php echo $childunlink_url ?>',{category:category,userid:userid},"post",childunlinkeddata,ajaxdefaulterrorcallback,false);
//            function linkeddata(data)
//            {
//                 linkedids = $.map(JSON.parse(data),function(index,value){
//                     return index
//                 });
//            }
//            function childunlinkeddata(data)
//            {
//                 childunlinks = $.map(JSON.parse(data),function(index,value){
//                     return index
//                 });
//            } 

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
                    if($.inArray(folderinfodata['id'],linkedids) !=-1)
                        islinked = true;

                        isrootfolder = false;
                    ajaxcall(getparentdataurl,null,"get",function(parentdata){
                        ajaxcall(gerfilesurl,null,"get",getfiles,ajaxdefaulterrorcallback);
                        var parentdata = $.parseJSON(parentdata);
                        $('.nav-title').html('<a href="javascript:void(0)" class="folder" data-id="'+parentdata["id"]+'">'+parentdata["name"]+'</a> &lsaquo;&lsaquo; '+
                                '<a href="javascript:void(0)" class="folder" data-id="'+folderinfodata["id"]+'"><strong>'+folderinfodata["name"]+'</strong></a>');
                    },ajaxdefaulterrorcallback);
                }else{
                    islinked = false;
                    isrootfolder = true;
                    ajaxcall(gerfilesurl,null,"get",getfiles,ajaxdefaulterrorcallback);
                    $('.nav-title').html('<a href="javascript:void(0)" class="folder" data-id="'+folderinfodata["id"]+'"><strong>'+folderinfodata["name"]+'</strong></a>');
                }
             }
             function getfiles(data){
                 var getfileinfo = '<?php echo $foulderinfo?>';
                var files = $.parseJSON(data);
                if(files[0] != undefined){
                    if(files[0].error_code == "403"){
                        setTimeout(ajaxcall(gerfilesurl,null,"get",getfiles,ajaxdefaulterrorcallback),3000);
                        return;
                    }
                }
                console.log(linkedids);
                console.log("unlinksChildLinks: "+childunlinks);
                 $('.layout-grid').empty();
                 if($.inArray( "create_folder", driveopt ) !=-1)
                    files.unshift({name:"Add folder",id:"",mimeType:"application/addfoulder",thumbnail:"driveassets/driveimages/foulder_add_icon.png",opt:false});
                 if(rootfoulder !=folderid)
                    files.unshift({name:"Previous folder (My Drive)",id:parentid,mimeType:"application/pre",thumbnail:"driveassets/driveimages/foulder_icon.png",opt:false});

                var fileopt = ["<a href='javascript:void(0)' id='previewfile'><span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span>Preview</a>"];
                var folderopt = [];

                $.each(driveopt,function(index,value){
                    if(value == "rename"){
                        fileopt.push("<a href='javascript:void(0)' data-toggle='modal' data-target='#rename'><span class='glyphicon glyphicon-tag' aria-hidden='true'></span>"+value.charAt(0).toUpperCase()+value.slice(1)+"</a>");
                        folderopt.push("<a href='javascript:void(0)' data-toggle='modal' data-target='#rename'><span class='glyphicon glyphicon-tag' aria-hidden='true'></span>"+value.charAt(0).toUpperCase()+value.slice(1)+"</a>");
                    }/*else if(value == "delete"){
                        fileopt.push("<a href='javascript:void(0)' data-toggle='modal' data-target='#delete'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span>"+value.charAt(0).toUpperCase()+value.slice(1)+"</a>");
                        folderopt.push("<a href='javascript:void(0)' data-toggle='modal' data-target='#delete'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span>"+value.charAt(0).toUpperCase()+value.slice(1)+"</a>");
                    }*/
                });
                var html = "";
                addCustomThumbnail(files);
                $.each(files,function(index,value){

                  function getlinkedfolders(){
                    linkstates = setlinkedstate(islinked,linkedids,childunlinks,value['id'],parentid);
                    if((!islinked && isrootfolder) || (islinked && ($.inArray(parentid,childunlinks) ==-1) ))
                      folderopt.unshift("<a href='javascript:void(0)' data-linkstate='"+linkstates.linkstate+"' data-toggle='modal' data-target='#linkfolder'><span class='glyphicon glyphicon-check' aria-hidden='true'></span>"+linkstates.linkstate+"</a>")  
                    return folderopt;
                 }
                    var opt = getfiletype(value['mimeType'])=="folder"?getlinkedfolders():fileopt; 

                    html += '<div class="entry '+getfiletype(value['mimeType'])+' '+linkstates.linkedclass+'" data-id="'+value['id']+'" data-name="'+value['name']+'">\n\
                                 <div class="entry_block" style="width: 131px;">';
                     if(getfiletype(value['mimeType']) != "parentfolder" && getfiletype(value['mimeType']) != "newfolder"){
                         html +='<div class="entry_edit"><a class="entry_edit_menu show-pop-dropdown" data-placement="bottom-left">\n\
                                   <i class="glyphicon glyphicon-menu-down"></i></a>';
                         html +='<div  class="webui-popover-content"><ul class="dropdown-menu" >';
                         $.each(opt,function(i,v){
                                  html +='<li data-name="'+value['name']+'" data-id="'+value['id']+'" data-type="'+value['mimeType']+'">'+v+'</li>';
                         });
                         folderopt.shift();
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
                   linkstates = "";   
                 });
                 $('.layout-grid').html(html);
                 $('.overlay').hide("fast");
                 $('.newfolder').insertAfter($(".layout-grid .folder").last());
                 /* toll tip*/
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
             }
          }

          var ajaxdefaulterrorcallback = function(xhr, ajaxOptions, thrownError){
             console.log(xhr.status);
             console.log(thrownError);
             console.log(xhr.responseText);
              if($.trim(thrownError) !="abort"){
                 $('.loader-contenar').hide();
                 $('.reload').show();
             }
          };


          function setlinkedstate(islinked,linkedids,unlinkids,folderid,fileparentid){
            this.linkst = {};
                if(islinked && $.inArray(fileparentid,childunlinks) == -1){
                    this.linkst.linkstate = $.inArray(folderid, unlinkids) ==-1 ? "Unlink Folder":"Link Folder";
                    this.linkst.linkedclass = $.inArray(folderid, unlinkids) ==-1 ? "isselected":"";
                }else{
                    this.linkst.linkstate = $.inArray(folderid, linkedids) !=-1 ? "Unlink Folder":"Link Folder";
                    this.linkst.linkedclass = $.inArray(folderid, linkedids) !=-1 ? "isselected":"";
                }
            return this.linkst;
          }


          $('.nav-home').click(function(){
               clearajaxcalls(allajaxcalls);
               $('.overlay').show("fast");
               loaddrive(rootfoulder,rootfoulder,category,userid);
          });
           $('.nav-refresh').click(function(){
                clearajaxcalls(allajaxcalls);
               $('.overlay').show("fast");
               loaddrive(rootfoulder,$('.nav-title a').last().data('id'),category,userid);
           });
           $(document).on("click",".folder,.parentfolder",function(){
                clearajaxcalls(allajaxcalls);
                $('.overlay').show("fast");
                loaddrive(rootfoulder,$(this).data('id'),category,userid);
           });
           $('.nav-driveframeclose').click(function(){
               parent.$.fn.showDrieFrame.close()
           });
          $('.reload').click(function(){
               clearajaxcalls(allajaxcalls);
                $('.overlay').show("fast");
                $('.overlay .loader-contenar').show();
                $(this).hide();
                loaddrive(rootfoulder,progressid,category,userid);
          });
          // add out custome thumbnailLinks on files*/
         function addCustomThumbnail(data){
            $.each($.makeArray(data),function(index,value){
                if((value.mimeType).toLowerCase()=="application/wav" || (value.mimeType).toLowerCase()=="application/aac" || (value.mimeType).toLowerCase()=="application/mp3" || (value.mimeType).toLowerCase()=="application/mpeg" || (value.mimeType).toLowerCase()=="application/ogg" || (value.mimeType).toLowerCase()=="application/wav" || (value.mimeType).toLowerCase()=="application/webm" || (value.mimeType).toLowerCase()=="application/aiff" )
                {
                   value.thumbnail = "driveassets/driveimages/File-Audio-icon.png";

                }if((value.mimeType).toLowerCase()=="application/zip")
                {
                   value.thumbnail = "driveassets/driveimages/zip-con.png";

                }if((value.mimeType).toLowerCase()=="text/xml")
                {
                   value.thumbnail = "driveassets/driveimages/xml-icon.png";

                }else if ((value.mimeType).toLowerCase() == "application/vnd.google-apps.folder")
                {
                    value.thumbnail = "driveassets/driveimages/foulder_icon.png"

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

         $('#rename,#delete,#linkfolder').on("show.bs.modal",function(event){
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
//             else if(modal.attr("name") == "link"){
//                modal.find('#link').removeClass("Unlink Link");
//                modal.find('#link').html(target.data('linkstate').split(" ")[0]);
//                modal.find('#link').addClass(target.data('linkstate').split(" ")[0])
//             }

         });
         $('#renamebtn').on("click",function(){
              clearajaxcalls(allajaxcalls);
             $('.overlay').show("fast");
             $('#rename').modal('hide');
             var renamelink = '<?php echo $rename_url ?>'+targetfid+"/"+$('#renamebox').val()+"/"+'<?php echo $row['notes'] ?>';
             ajaxcall(renamelink,null,"get",function(data){
                  loaddrive(rootfoulder,progressid,category,userid);
             },ajaxdefaulterrorcallback);
         })

         $('#deletebtn').on("click",function(){
             clearajaxcalls(allajaxcalls);
             $('.overlay').show("fast");
             $('#delete').modal('hide');
             var deletedlink = '<?php echo $delete_url ?>'+targetfid;
             ajaxcall(deletedlink,null,"get",function(data){
                  loaddrive(rootfoulder,progressid,category,userid);
             },ajaxdefaulterrorcallback);
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
                  loaddrive(rootfoulder,progressid,category,userid);
             },ajaxdefaulterrorcallback);
         });

//         $('#link').on("click",function(){
//              clearajaxcalls(allajaxcalls);
//             $('.overlay').show("fast");
//             $('#linkfolder').modal('hide');
//             var url = "";
//             if(isrootfolder){
//                 status = $(this).hasClass("Link")?"check":"uncheck";
//                 url = '<?php echo $parentlink_url ?>';
//             }else{
//                 status = $(this).hasClass("Link")?"check":"uncheck";
//                 url = '<?php echo $childunlink_url ?>';
//             }
//
//
//             ajaxcall(url,{category:category,userid:userid,action:'save',status:status,folderid:targetfid},"post",function(data){
//                  loaddrive(rootfoulder,progressid,category,userid);
//             },ajaxdefaulterrorcallback);
//
//         });
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
                  try{
                    var patt = new RegExp(inputval,"i");
                    var res = patt.test($(this).data('name'));
                    return res;
                  }catch(e){
                      console.log("catcherror");
                      return null;
                  }
              }).not('.parentfolder,.newfolder').show();
              if(inputval =="")
                $('.parentfolder,.newfolder').show();
          })
          $(document).on("click",".file,#previewfile",function(){
               $('a.show-pop-dropdown').webuiPopover('hide');
              var fileviewurl = "";
              if($(this).parent().is('li')==true)
                 fileviewurl = '<?php echo $fileview_url ?>'+$(this).parent('li').data('id');
              else
                 fileviewurl = '<?php echo $fileview_url ?>'+$(this).data('id');

            var url ="curlfileview.php?url="+fileviewurl;
            var lightbox = lity();
            lightbox(url);
            $(document).on('click', '[data-lightbox]', lightbox);
            //$('.lity').hide();
            //$('.overlay').show("fast");
         });
         $('.sideicon').click(function(){
             if($('.sidemenu').hasClass('in')){
                $('.sidemenu').removeClass("in").addClass('out');
                $('.list-container, .list-container .nav-header,.overlay').animate({width:"100%"});
             }else{
                 $('.sidemenu').removeClass("out").addClass('in');
                $('.list-container, .list-container .nav-header,.overlay').animate({width:"80%"});
             }
         });
         $('.listitems > ul').append(function(){
            var html = "";
            var items = JSON.parse('<?php echo json_encode($category_arr) ?>');
            $.each(items,function(index,value){
                var itemname = value['title'].replace("_"," ").capitalizeFirstLetter();
                html += "<li><a href='#' data-category='"+value['title']+"' data-frame='"+value['rootid']+"'>"+itemname+"</a></li>";
            });
            return html;
         });

         $('.listitems > ul li').first().addClass("active");
         $('.listitems > ul li > a').on("click",function(){
            if(currentajaxcall != null){
                currentajaxcall.abort();
            }
            $('.overlay').show("fast");
            $('.overlay .loader-contenar').show();
            $(this).parents('ul').find('li').removeClass('active');
            $(this).parent().addClass("active");
            rootfoulder = $(this).data('frame');
            category = $(this).data('category');
            loaddrive(rootfoulder,rootfoulder,category,userid);
         });
         $('.listitems > ul li > a').first().trigger("click");                  
     });
     function frameload(){
        $('.lity iframe').contents().find("body>img").parents("body").css("text-align","center");
     }
    function imgError(image) {
        //image.onerror = "";
        if($(image).data("thumerror") == undefined){
             $(image).data("thumerror","error");
            image.src = "https://drive.google.com/thumbnail?id="+$(image).parents('.entry').data('id');
        }else{
            image.src = "driveassets/driveimages/defaultfile.png";
        }
        return true;
    }
    String.prototype.capitalizeFirstLetter = function() {
        return this.charAt(0).toUpperCase() + this.slice(1);
    }
</script>

 <!--<a href="javascript:void(0)" class="folder" ><strong>Start</strong></a>-->
<div class="link_content" style="height: 500px;">
    <aside class="sidemenu in">
        <h4>Menu</h4>
        <div class="listitems">
            <ul>
            </ul>
        </div>
    </aside>
    <div class="list-container uyd-grid">
        <div class="nav-header">
            <a href="#" class="sideicon" id="sideicon"></a>
            <a class="nav-home" href="javascript:void(0)" title="Back to our first folder"><span class="glyphicon glyphicon-home pull-left"></span></a>
            <a href="javascript:void(0)" class="nav-driveframeclose" title="Close"><span class="glyphicon glyphicon-remove-circle"></span></a>
            <a href="javascript:void(0)" class="nav-refresh" title="Refresh"><span class="glyphicon glyphicon-refresh"  ></span></a>
            <a href="javascript:void(0)" class="nav-search"  data-placement="bottom-left"><span class="glyphicon glyphicon-search" ></span></a>
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
            <div class="username pull-right"><span class="glyphicon glyphicon-user pull-left"></span><?php echo $user_name; ?></div>
            <div class="nav-title"></div>
        </div>
        <div class="ajax-filelist">
            <div class="overlay">
                <div class="loader-contenar">
                    <div class="drive"><img src="driveassets/driveimages/drive.png" width="83px"/></div>
                    <div class="circle"></div>
                </div>
                <div class="reload"><a href="javascript:void(0)"><img width="100px" src="driveassets/driveimages/reload.png"></a>
                </div>
            </div>    
            <div class="files layout-grid">
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
<!--    <div class="modal fade" name = "link" id="linkfolder" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="vertical-alignment-helper">
            <div class="modal-dialog vertical-align-center modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                         <h4 class="modal-title" id="myModalLabel">Link Folder</h4>
                    </div>
                    <div class="modal-body">
                        <div id="deletemessage">Do You want to Link this folder</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="link"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>-->
   </body>
</html>      