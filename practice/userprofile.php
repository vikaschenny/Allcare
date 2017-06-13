<?php 
$base_url = "//".$_SERVER['SERVER_NAME'].'/practice/'; 
require_once("verify_session.php");
require_once("../library/sqlCentralDB.inc");
global $sqlconfCentralDB;
?>
<?php
    $postarr = $_POST['practice'];
    $practiceId = '';
    $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practiceID'");
    while($row = sqlFetchArray($query)){
        $practiceId = $row['title'];
    }
     $query = "SELECT fname,mname,lname,practiceId,groupid, practiceredirects FROM allcareobjects WHERE userid = '".$_SESSION['portal_userid']."' 
               AND objecttype='user' AND practiceId ='". $practiceId ."'";
     $stmt_user = $sqlconfCentralDB->prepare($query) ;
     $stmt_user->execute();
     $user = $stmt_user->fetchObject();
     $pracArr = array();
     if($user):
        $pracArr['fname'] = $user->fname;
        $pracArr['mname'] = $user->mname;
        $pracArr['lname'] = $user->lname;
        
        $pracArr['practice'] = $user->practiceredirects;
        $pracArr['groupid'] = $user->groupid;
     endif;
 ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="chrome=1">
  <title>User Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="./css/toastr.css">
  <script type="text/javascript" src="<?php echo $base_url; ?>assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo $base_url; ?>assets/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="<?php echo $base_url; ?>./js/toastr.js"></script>
  <style>
        body{
          overflow-x: hidden;
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
            height: 100%
        }

        #sidebar-wrapper {
            margin-left: -235px;
            left: 53px;
            width: 235px;
            background: #46a1b4;//#222;
            position: fixed;
            height: 100%;
            z-index: 1;
            transition: all .4s ease 0s;
        }

        .sidebar-nav {
            display: block;
            float: left;
            width: 235px;
            list-style: none;
            margin: 0;
            padding: 0;
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
            color: #fff;
            display: block;
            float: left;
            text-decoration: none;
            width: 235px;
            background: #46a1b4;
            border-top: 1px solid #54afc2;
            border-bottom: 1px solid #3893a6;
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
          color: #fff;
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
        #content {
            position: relative;
            padding: 15px 10px 0 15px;
        }
        
        #content > div{
            display: none;
        }
        #content .show{
            display: block;
        }
        
        fieldset 
	{
		border: 1px solid #ddd !important;
		margin: 0;
		xmin-width: 0;
		padding: 10px;       
		position: relative;
		border-radius:4px;
		background-color:#f5f5f5;
		padding-left:10px!important;
                margin: 10px 15px 10px 10px;
	}	
	
        legend
        {
                font-size:14px;
                font-weight:bold;
                margin-bottom: 0px; 
                width: 35%; 
                border: 1px solid #ddd;
                border-radius: 4px; 
                padding: 5px 5px 5px 10px; 
                background-color: #ffffff;
        }
        
        .btn-glyphicon { padding:8px; background:#ffffff; margin-right:4px; }
        .icon-btn { padding: 1px 15px 3px 2px; border-radius:50px;}
        button.close{
            opacity: 1;
        }
        @media only screen and (max-width:767px){
            .removerow{
                margin-top: 8px;
                margin-left: 50%;
            }
        }
        
        //required field
        
        input, textarea, button { margin-top:10px }

/* Required field START */

        .required-field-block {
            position: relative;   
        }

        .required-field-block .required-icon {
            display: inline-block;
            vertical-align: middle;
            margin: -0.25em 0.25em 0em;
            background-color: #E8E8E8;
            border-color: #E8E8E8;
            padding: 0.5em 0.8em;
            color: rgba(0, 0, 0, 0.65);
            text-transform: uppercase;
            font-weight: normal;
            border-radius: 0.325em;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            box-sizing: border-box;
            -webkit-transition: background 0.1s linear;
            -moz-transition: background 0.1s linear;
            transition: background 0.1s linear;
            font-size: 75%;
        }

        .required-field-block .required-icon {
            background-color: transparent;
            position: absolute;
            top: 0em;
            right: 0em;
            z-index: 10;
            margin: 0em;
            width: 30px;
            height: 30px;
            padding: 0em;
            text-align: center;
            -webkit-transition: color 0.2s ease;
            -moz-transition: color 0.2s ease;
            transition: color 0.2s ease;
        }

        .required-field-block .required-icon:after {
            position: absolute;
            content: "";
            right: 16px;
            top: 1px;
            z-index: -1;
            width: 0em;
            height: 0em;
            border-top: 0em solid transparent;
            border-right: 30px solid transparent;
            border-bottom: 30px solid transparent;
            border-left: 0em solid transparent;
            border-right-color: inherit;
            -webkit-transition: border-color 0.2s ease;
            -moz-transition: border-color 0.2s ease;
            transition: border-color 0.2s ease;
        }

        .required-field-block .required-icon .text {
                color: #B80000;
                font-size: 26px;
                margin: -3px 0 0 -19px;
        }
        
        
  </style>
  <script>
       var practiveobj = '<?php echo $pracArr['practice'] ?>';
       
        function showloader(){
            $('#loader').hide();
        }
        function activemenu(){
            
          setTimeout(function(){
               if($(window).width() > 767)
                    $("#wrapper").addClass("active");
           },500)
        }
        $(function(){
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "2000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
            
              addptc(practiveobj);
            function ajaxcall(url,data,type,callback,errorcallback){
                $.ajax({url:url,type:type,xhrFields: {withCredentials: true},data:data, crossDomain: true,error:errorcallback,success:callback});
            }
            var ajaxdefaulterrorcallback = function(xhr, ajaxOptions, thrownError){
                console.log(xhr.status);
                console.log(thrownError);
                console.log(xhr.responseText);
            };
             $('[data-toggle="tooltip"]').tooltip(); 
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("active");
            });
              
            $('.sidebar-nav li a').click(function(evt){
                evt.preventDefault();
                var getindex = $(this).parent('li').index();
            })
            function newpractice(element,valobj){
                var ptcnumber = (element.children(".form-group").length)+1;
                practicehtml = '<div class="form-group">\n\
                                    <label class="control-label col-sm-2" for="practicid'+ptcnumber+'">Practice ID : </label>\n\
                                    <div class="col-sm-4 required-field-block">\n\
                                        <input type="text" class="form-control" value="'+valobj[0]+'" id="practicid'+ptcnumber+'" placeholder="Enter Practice id">\n\
                                        <div class="required-icon">\n\
                                           <div class="text">*</div>\n\
                                        </div>\n\
                                    </div>\n\
                                    <label class="control-label col-sm-2" for="username'+ptcnumber+'">User Name : </label>\n\
                                    <div class="col-sm-3 required-field-block">\n\
                                        <input type="text" class="form-control" value="'+valobj[1]+'" id="username'+ptcnumber+'" placeholder="Enter Username">\n\
                                        <div class="required-icon">\n\
                                           <div class="text">*</div>\n\
                                        </div>\n\
                                    </div>\n\
                                    <a href="#" class="btn btn-danger removerow"  data-placement="bottom" data-toggle="tooltip" title="remove row"><i class="glyphicon glyphicon-trash"></i></a>\n\
                                </div>';
                return practicehtml;
            }
            
            $('#addpractice').click(function(event){
                event.preventDefault();
                $(this).parents("#practices").find("#grouppractices").append(newpractice($('#grouppractices'),["",""]));
                $("#grouppractices").find(".removerow").css("cursor","pointer");
                
            });
            
            function addptc(ptcobj){
                if(ptcobj.length !=0)
                   $("#grouppractices").html("");
                
                var fieldcount = 0;
                var fielddata = [];
                if(ptcobj.length !=0) ptcobj = JSON.parse(ptcobj);
                $.each(ptcobj,function(index,value){
                    fieldcount++;
                    //console.log(index + " : " + value);
                    fielddata.push(value);
                    if(index.indexOf("username-") == 0){
                       //console.log(fielddata);
                       $("#grouppractices").append(newpractice($('#grouppractices'),fielddata));
                        fielddata = [];
                    }
                });
            }
            
            $("#update").click(function(event){
                var practicedata = {}
                var self = $(this);
                var completed = true;
                $(".required-field-block input").each(function(index,value){
                   if($(this).val().trim() ==""){
                        completed = false;
                        $(this).css("border-color","red");
                   }else{
                        $(this).css("border-color","#ccc");
                   }
                       
               })
               if(!completed){
                 toastr.error('', 'Please fill the all required fields');
                 return;
               }
                practicedata.fname = $("#profileform #firstname").val();
                practicedata.mname = $("#profileform #middlename").val();
                practicedata.lname = $("#profileform #lastname").val();
                practicedata.practice = {};
                $('#grouppractices').children(".form-group").each(function(index,element){
                    var pid = $(this).find("input[id^='practicid']").val();
                    var puser = $(this).find("input[id^='username']").val();
                    practicedata.practice['url-'+(index+1)] = pid;
                    practicedata.practice['username-'+(index+1)] = puser;
                })
                practicedata.groupid = $("#profileform #groupid").val();
                 self.button('loading');
                ajaxcall("userprofile/userauth.php",{practice:practicedata},"post",function(data){
                         self.button('reset');
                         console.log(data);
                         var message = $.parseJSON(data);
                         if(message["type"] == "error"){
                            toastr.error('', message["message"]);
                             var strMessage = message["message"].split(":")[1].split(",");
                             $('#grouppractices').find("input[type='text']").each(function(index,value){
                                //alert($(this).val());
                                if($.inArray($(this).val(),strMessage) != -1){
                                    $(this).css("border-color","red");
                                }
                            });
                         }   
                         else{
                            toastr.success('', message["message"]);
                         }
                       
                    },function(xhr, ajaxOptions, thrownError){
                         self.button('reset');
                         toastr.error('', 'Error to update.');
                });
            });
            
            $(document).on("click",".removerow",function(event){
                event.preventDefault();
                if($("#grouppractices").children().length == 1)
                    return;
                
                $(this).parent(".form-group").remove();
                $("#grouppractices").children().each(function(index,element){
                    $(this).find("[for^='practicid']").attr("for","practicid"+(index+1));
                    $(this).find("[id^='practicid']").attr("id","practicid"+(index+1));
                    $(this).find("[for^='username']").attr("for","username"+(index+1));
                    $(this).find("[id^='username']").attr("id","username"+(index+1));
                });
                if($("#grouppractices").children().length == 1)
                   $("#grouppractices").find(".removerow").css("cursor","not-allowed");
            });
        });
   </script>
</head>
<body>
    <div id="wrapper">
            <div id="sidebar-wrapper">
                <ul id="sidebar_menu" class="sidebar-nav">
                   <li class="sidebar-brand"><a id="menu-toggle" href="#">Menu<span id="main_icon" class="glyphicon glyphicon-align-justify"></span></a></li>
                </ul>
                <ul class="sidebar-nav" id="sidebar">
                    <li><a class="active">Profile<span class="sub_icon glyphicon glyphicon-link"></span></a></li>
                </ul>
            </div>
            <div id="page-content-wrapper">
                <div class="page-content inset">
                        <div class="row">
                                <div id="content">
                                    <div class="show">
                                        <form class="form-horizontal" id="profileform">
                                            <div class="form-group">
                                                <label class="control-label col-sm-3" for="firstname">First Name:</label>
                                                <div class="col-sm-9 required-field-block">
                                                  <input type="text" class="form-control" id="firstname" placeholder="Enter firstname" value="<?php echo $_SESSION['portal_fname']; ?>">
                                                  <div class="required-icon">
                                                        <div class="text">*</div>
                                                  </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-3" for="middlename">Middle Name:</label>
                                                <div class="col-sm-9">
                                                  <input type="text" class="form-control" id="middlename" placeholder="Enter middlename" value="<?php echo $_SESSION['portal_mname']; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-3" for="lastname">Last Name:</label>
                                                <div class="col-sm-9 required-field-block">
                                                  <input type="text" class="form-control" id="lastname" placeholder="Enter lastname" value="<?php echo $_SESSION['portal_lname']; ?>">
                                                  <div class="required-icon">
                                                        <div class="text">*</div>
                                                  </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <fieldset class="col-md-10 col-md-offset-1">    	
                                                        <legend>Practices</legend>
                                                        <div class="panel panel-default">
                                                            <div class="panel-body" id="practices">
                                                                <div id="grouppractices">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-2" for="practicid1">Practice ID : </label>
                                                                        <div class="col-sm-4 required-field-block">
                                                                          <input type="text" class="form-control" id="practicid1" placeholder="Enter Practice id">
                                                                          <div class="required-icon">
                                                                                <div class="text">*</div>
                                                                          </div>
                                                                        </div>
                                                                        <label class="control-label col-sm-2" for="username1">User Name : </label>
                                                                        <div class="col-sm-3 required-field-block">
                                                                          <input type="text" class="form-control" id="username1" placeholder="Enter Username">
                                                                          <div class="required-icon">
                                                                                <div class="text">*</div>
                                                                          </div>
                                                                        </div>
                                                                        <a href="#" class="btn btn-danger removerow" data-toggle="tooltip"  data-placement="bottom" title="remove row"><i class="glyphicon glyphicon-trash"></i></a>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group text-center">
                                                                    <a class="btn icon-btn btn-success btn-sm" href="#" id="addpractice"><span class="glyphicon btn-glyphicon glyphicon-plus img-circle text-success"></span>Add Practice</a>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-sm-2" for="groupid">Group ID : </label>
                                                                    <div class="col-sm-10 required-field-block">
                                                                      <input type="text" class="form-control" id="groupid" placeholder="Enter Groupid" value="<?php echo $pracArr['groupid'] ?>">
                                                                      <div class="required-icon">
                                                                            <div class="text">*</div>
                                                                      </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                </fieldset>
                                                <div class="clearfix"></div>
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-primary"  data-loading-text="<i class='fa fa-spinner fa-spin'></i> updateing..." id="update">Update</button>
                                                </div>
                                                
                                            </div>
                                        </form>
                                    </div>
                                    
                                </div>
                        </div>
                </div>
            </div>        
        </div>
  </body>
</html>
