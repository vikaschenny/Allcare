$(function(){
     $('[data-toggle="tooltip"]').tooltip();
     //options for action completed message alert
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
      
      // message text editer options
      var testexitremoveopt = {"texteffects":false,"aligneffects":false,"textformats":false,"fonteffects":false,"actions":false,"insertoptions":false,"extraeffects":false,"advancedoptions":false,"screeneffects":false,"ol":false,"ul":false,"undo":false,"redo":false,"l_align":false,"r_align":false,"c_align":false,"justify":false,"insert_img":false,"hr_line":false,"block_quote":false,"source":false,"strikeout":false,"indent":false,"outdent":false,"fonts":false,"styles":false,"print":false,"rm_format":false,"status_bar":true,"font_size":false,"splchars":false,"insert_table":false,"select_all":false}

     var messagecount = null, allmessagesobject = null, filtermessagedata=null, viewcount=10, startindex=0, endindex = viewcount, startTo=0,openchild=null,listdata = null,listsetintervel=null,deletecount=0;
    //global ajax call function
    function ajaxcall(url,data,type,callback,errorcallback){
         $.ajax({url:url,type:type,xhrFields: {withCredentials: true},data:data, crossDomain: true,error:errorcallback,success:callback});
     }
     // global ajax error default call
     var ajaxdefaulterrorcallback = function(xhr, ajaxOptions, thrownError){
         console.log(xhr.status);
         console.log(thrownError);
         console.log(xhr.responseText);
         toastr.error('', xhr.responseText);
     };
     
     // split the window and resize 
     splitpenal(109,$(".panel-top"),$(".panel-bottom"));
     
     // get all the messages for database.
     ajaxcall("msg_list_ajax.php",null,"post",function(data){
          allmessagesobject = $.parseJSON(data)|| [];
          showMessages(allmessagesobject,startindex,endindex,startTo);
          //console.log(data);
      },function(xhr, ajaxOptions, thrownError){
          $('#loader').hide();
          $('#mloadingerror').show();
      });
    $.fn.extend({
        treed: function (o) {

          var openedClass = 'glyphicon-minus-sign';
          var closedClass = 'glyphicon-plus-sign';

          if (typeof o != 'undefined'){
            if (typeof o.openedClass != 'undefined'){
            openedClass = o.openedClass;
            }
            if (typeof o.closedClass != 'undefined'){
            closedClass = o.closedClass;
            }
          };

            //initialize each of the top levels
            var tree = $(this);
            tree.addClass("tree");
            //tree.find('li').has("ul").each(function () {
                var branch = tree.find('li'); //li with children ul
                branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
                branch.addClass('branch');
                $(tree).on('click','.branch', function (e) {
                    var $self = $(this);
                    if (this == e.target) {
                        var icon = $(this).children('i:first');
                        icon.toggleClass(openedClass + " " + closedClass);
                        $(this).children().children().toggle();
                        if($(this).children("ul").is(":empty")){
                            icon.addClass("fa fa-spinner fa-spin fa-1x fa-fw")
                            $.ajax({url:o.subfolderurl+"/"+$(this).data("folderid")+"/folders",data:null,type:"get",success: function (data, textStatus, jqXHR) {
                                    var subfolderdata = $.parseJSON(data);
                                    var subfolderhtml = "";
                                    $.each(subfolderdata,function(index,value){
                                        subfolderhtml += '<li class="branch" data-folderid='+value['id']+'><a href="#">'+value["name"]+'</a>  <input type="radio" name="movefile"><ul></ul></li>';
                                    });
                                    if(subfolderdata.length == 0){
                                       $self.find('a').append(" <span style='color:red;'>(empty)</span>");
                                       subfolderhtml = " ";
                                    }
                                    $self.children("ul").html(subfolderhtml);
                                    $self.children("ul").find('li').prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
                                    icon.removeClass("fa fa-spinner fa-spin fa-1x fa-fw");
                                    //console.log("subfolders: " + data);
                                },error: function (jqXHR, textStatus, errorThrown) {
                                   icon.removeClass("fa fa-spinner fa-spin fa-1x fa-fw") 
                                }
                            })
                        }
                        $self.children("input[type=radio]").prop("checked",true);
                    }
                });
                branch.children().children().toggle();
                
            //});
            //fire event from the dynamically added icon
            tree.on('click','.branch .indicator', function () {
                $(this).closest('li').click();
            });
            //fire event to open branch if the li contains an anchor instead of text
            tree.on('click','.branch>a', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });

        }
    });
    
      // get selectboxs options for database.
      getoptdata()
     
     // message option show all justmine options.
     $(document).on('click', '.bs-dropdown-to-select-group .dropdown-menu li a', function() {
         var label = $(this).html();
         var optionval =  $(this).data("value");
         $(this).closest('.bs-dropdown-to-select-group').find('[data-toggle="dropdown"]').find('#label').html(label).data("value",optionval);
         restcounts();
         showMessages(allmessagesobject,startindex,endindex,startTo);
     });
     
     // place holder text in message box 
     $(document).on("focusout",".entertext",function(){
        var element = $(this);        
        if (!element.text().replace(" ", "").length) {
            element.empty();
        }
     });
      
      $('#linkmodal').on("show.bs.modal", function(event){
          var target = $(event.relatedTarget);
          var modal = $(this);
          modal.find('#linksubmit').data("mchild",target.parents("tr").attr("class"));
          $('.required-icon').tooltip({
            placement: 'left',
            title: 'Required field'
          });
          
      });
      
      // remove default action on hyperlink on messages
      function hyperlinkclick(element,event){
          event.preventDefault();
      }
      
      // toggle menu
      $("#menu").click(function(){
          $(".sm-side").toggleClass("toggleindexsm");
          $(".inbox-head").toggleClass("toggleradies");
      });
      
      $(document).on("click","#linksubmit",function(){
         
         var linktitle = $("#hypertitle").val();
         var linkurl = $("#hyperurl").val();
         if(linktitle.trim() == "" && linkurl.trim() == ""){
             $("#hypertitle").css("border-color","red");
             $("#hyperurl").css("border-color","red");
             return;
         }else if(linktitle.trim() == ""){
             $("#hypertitle").css("border-color","red");
             return;
         }else if(linkurl.trim() == ""){
             $("#hyperurl").css("border-color","red");
             return;
         }
         //console.log($("."+$(this).data("mchild")).find(".entertext"));
         $("."+$(this).data("mchild")).find(".entertext").focus();
      });
      
     // child message row action of click message
     $(document).on("click",".messagerow",function(evt,messageType,toggle){
         //console.log(messageType + " " + toggle);
         toggle = toggle == undefined? true:toggle;
         var messageid = $(this).data("id");
         var messagedata = getelementdata(allmessagesobject,messageid)
         var editmload = false;
         var messageDetails = '<tr class="child'+messageid+'" style="display:none;">\n\
                                    <td colspan="6">\n\
                                        <div class="info" style="display:none;">\n\
                                            <div class="panel with-nav-tabs panel-primary">\n\
                                                <div class="panel-heading">\n\
                                                     <ul class="nav nav-tabs">\n\
                                                        <li class="active">\n\
                                                            <a href="#tab1default" data-toggle="tab">Message information</a>\n\
                                                        </li>\n\
                                                    </ul>\n\
                                                </div>\n\
                                                <div class="panel-body">\n\
                                                    <div class="tab-content">\n\
                                                        <div class="tab-pane fade in active" id="tab1default">\n\
                                                            <div id="loader2">\n\
                                                                <div class="ajax-spinner-bars">\n\
                                                                    <div class="bar-1"></div>\n\
                                                                    <div class="bar-2"></div>\n\
                                                                    <div class="bar-3"></div>\n\
                                                                    <div class="bar-4"></div>\n\
                                                                    <div class="bar-5"></div>\n\
                                                                    <div class="bar-6"></div>\n\
                                                                    <div class="bar-7"></div>\n\
                                                                    <div class="bar-8"></div>\n\
                                                                    <div class="bar-9"></div>\n\
                                                                    <div class="bar-10"></div>\n\
                                                                    <div class="bar-11"></div>\n\
                                                                    <div class="bar-12"></div>\n\
                                                                    <div class="bar-13"></div>\n\
                                                                    <div class="bar-14"></div>\n\
                                                                    <div class="bar-15"></div>\n\
                                                                    <div class="bar-16"></div>\n\
                                                                </div>\n\
                                                                <div id="loadertitle">Messages Loading...</div>\n\
                                                            </div>\n\
                                                            <div class="row">\n\
                                                                <div class="col-sm-2 col-xs-4">\n\
                                                                    <label>From</label>\n\
                                                                </div>\n\
                                                                <div class="col-sm-4 col-xs-8">'+
                                                                    messagedata.from+
                                                                '</div>\n\
                                                                <div class="col-sm-2 col-xs-4">\n\
                                                                    <label>Assigned To</label>\n\
                                                                </div>\n\
                                                                <div class="col-sm-4 col-xs-8">\n\
                                                                    <div class="minfo infoassigen">'+
                                                                        messagedata.assigned_to
                                                                    +'</div>\n\
                                                                    <div class="medit">\n\
                                                                        <select class="assignedto select2">\n\
                                                                            <option></option>\n\
                                                                        </select>\n\
                                                                    </div>\n\
                                                                </div>\n\
                                                            </div>\n\
                                                            <div class="row">\n\
                                                                <div class="col-sm-2 col-xs-4">\n\
                                                                    <label>Object Type</label>\n\
                                                                </div>\n\
                                                                <div class="col-sm-4 col-xs-8">\n\
                                                                    <div class="minfo infoobjtype">'+
                                                                        messagedata.object_type
                                                                    +'</div>\n\
                                                                    <div class="medit">\n\
                                                                        <select class="objecttype select2" disabled>\n\
                                                                            <option></option>\n\
                                                                        </select>\n\
                                                                    </div>\n\
                                                                </div>\n\
                                                                <div class="col-sm-2 col-xs-4">\n\
                                                                    <label>Linked To</label>\n\
                                                                </div>\n\
                                                                <div class="col-sm-4 col-xs-8">\n\
                                                                    <div class="minfo infolinkto">'+
                                                                        messagedata.linked_to
                                                                    +'</div>\n\
                                                                    <div class="medit">\n\
                                                                        <select class="linkedto select2" disabled>\n\
                                                                            <option></option>\n\
                                                                        </select>\n\
                                                                    </div>\n\
                                                                </div>\n\
                                                            </div>\n\
                                                            <div class="row">\n\
                                                                <div class="col-sm-2 col-xs-4">\n\
                                                                    <label>Message Type</label>\n\
                                                                </div>\n\
                                                                <div class="col-sm-4 col-xs-8">\n\
                                                                    <div class="minfo infomessagetype">'+
                                                                        messagedata.Message_type
                                                                    +'</div>\n\
                                                                    <div class="medit">\n\
                                                                        <select class="messagetype select2">\n\
                                                                            <option></option>\n\
                                                                        </select>\n\
                                                                    </div>\n\
                                                                </div>\n\
                                                                <div class="col-sm-2 col-xs-4">\n\
                                                                    <label>Date</label>\n\
                                                                </div>\n\
                                                                <div class="col-sm-4 col-xs-8"><div class="senddate">'+
                                                                    messagedata.date+
                                                                '</div></div>\n\
                                                            </div>\n\
                                                            <div class="row">\n\
                                                                <div class="col-sm-2 col-xs-4">\n\
                                                                    <label>Priority</label>\n\
                                                                </div>\n\
                                                                <div class="col-sm-4 col-xs-8">\n\
                                                                    <div class="minfo infopriority">'+
                                                                        messagedata.priority
                                                                    +'</div>\n\
                                                                    <div class="medit">\n\
                                                                        <select class="priority select2">\n\
                                                                            <option></option>\n\
                                                                        </select>\n\
                                                                    </div>\n\
                                                                </div>\n\
                                                                <div class="col-sm-2 col-xs-4">\n\
                                                                    <label>Status</label>\n\
                                                                </div>\n\
                                                                <div class="col-sm-4 col-xs-8">\n\
                                                                    <div class="minfo infostatus">'+
                                                                        messagedata.Status
                                                                    +'</div>\n\
                                                                    <div class="medit">\n\
                                                                        <select class="status select2">\n\
                                                                            <option></option>\n\
                                                                        </select>\n\
                                                                    </div>\n\
                                                                </div>\n\
                                                            </div>\n\
                                                            <div class="row">\n\
                                                                <div class="col-sm-2 col-xs-4">\n\
                                                                    <label>Content</label>\n\
                                                                </div>\n\
                                                                <div class="col-sm-10 col-xs-8">\n\
                                                                    <div class="minfo infocontent">'+
                                                                        removeendbr(messagedata.content.split("||")).join("<br>")
                                                                    +'</div>\n\
                                                                    <div class="medit nopadding">\n\
                                                                        <div class="customtextbox form-control">\n\
                                                                            <div class="">\n\
                                                                                <div class="panel-group">\n\
                                                                                    <div class="panel panel-default">\n\
                                                                                        <div class="panel-heading" style="padding:8px 11px 7px">\n\
                                                                                            <h4 class="panel-title">\n\
                                                                                                <a data-toggle="collapse" href="#collapse'+messageid+'">Previous Messages</a>\n\
                                                                                            </h4>\n\
                                                                                        </div>\n\
                                                                                        <div id="collapse'+messageid+'" class="panel-collapse collapse">\n\
                                                                                            <div class="panel-body nonedit">'+removeendbr(messagedata.content.split("||")).join("<br>")+'</div>\n\
                                                                                        </div>\n\
                                                                                    </div>\n\
                                                                                </div>\n\
                                                                            </div>\n\
                                                                            <div class="entertext" contenteditable="true" placeholder="Enter Message hear..." draggable="false"></div>\n\
                                                                        </div>\n\
                                                                    </div>\n\
                                                                </div>\n\
                                                            </div>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                                <div class="panel-footer">\n\
                                                    <div class="text-right">';
                                                    if( messagedata.Status == "done")
                                                        messageDetails += '<button class="btn btn-md btn-warning editm" data-disablededit="true" type="button"><i class="glyphicon glyphicon-edit"></i> Edit Message</button>';
                                                    else
                                                        messageDetails += '<button class="btn btn-md btn-warning editm" data-disablededit="false" type="button"><i class="glyphicon glyphicon-edit"></i> Edit Message</button>';
                                                    
                                                    messageDetails += '<button class="btn btn-md btn-warning savem" type="button" data-loading-text="'+"<i class='fa fa-spinner fa-spin '></i> Saveing Message"+'"><i class="glyphicon glyphicon-save"></i> Save Message</button>\n\
                                                        <button class="btn btn-md btn-danger closeinfo" type="button"><i class="glyphicon glyphicon-remove"></i> Close Message</button>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                        </div>\n\
                                    </td>\n\
                                </tr>';
        if(!($(this).next("tr").hasClass("child"+messageid))){
           $(messageDetails).insertAfter($(this));
           $(".entertext").Editor(testexitremoveopt);
           fillopts(listdata,$(".child"+messageid))
           /*listsetintervel = setInterval(function(){
               if(listdata != null)
                  fillopts(listdata,$(".child"+messageid))
           },500);*/
       }
        
        $('[data-toggle="tooltip"]').tooltip();
        if($(".child"+messageid).find(".info").css("display") == "block" && messageType == undefined)
            messageType = "close";
        
        if(toggle == true){
            $(".child"+messageid).slideToggle().find('.info').slideToggle();
            $(this).find(".indicater i").toggleClass("glyphicon glyphicon-chevron-down");
            $(this).find(".indicater i").toggleClass("glyphicon glyphicon-chevron-up");
        }
        
        if(messageType == "edit"){
            $(".child"+messageid).find('.info .minfo').hide();
            $(".child"+messageid).find('.info .medit').show();
            $(".child"+messageid).find(".editm").hide();
            $(".child"+messageid).find(".savem").show();
            if(listdata == null || editmload == false)
              $(".child"+messageid).find("#loader2").show();
          
            geteditdata(messageid,$(".child"+messageid));
            disabledRootEdit($(".child"+messageid).prev('tr').find('.editmessage'));  
        }else if(messageType == "close"){
            //console.log("closeslide");
            enabledRootEdit($(".child"+messageid).prev('tr').find('.editmessage'));
        }else{
            $(".child"+messageid).find('.info .minfo').show();
            $(".child"+messageid).find('.info .medit').hide();
            $(".child"+messageid).find(".editm").show();
            $(".child"+messageid).find(".savem").hide();
            enabledRootEdit($(".child"+messageid).prev('tr').find('.editmessage'));
        }
     });
     
     // show add messagepop 
     $('#addmessagemodal').on("show.bs.modal", function(event){
        var target = $(event.relatedTarget);
        var modal = $(this);
        listsetintervel = setInterval(function(){
               if(listdata != null)
                  addmessage();
           },500);
            function addmessage(){
                clearInterval(listsetintervel);
                modal.find(".modal-body").find("form").html(addMessageUI(listdata));
                modal.find(".modal-body").find("form .select2").select2({ dropdownParent: $('.modal'),placeholder : 'Please Select' });
                $("#addmessagecontent").Editor(testexitremoveopt);
                modal.find(".modal-body").find("#addobject").select2({ placeholder : 'Please Select' }).on("change",function(event){
                    var objvalue = $(this).val();
                    var getlinkval = listdata.object_value[objvalue];
                    var opts = "";
                    $.each(getlinkval,function(index,value){
                        opts +="<option value='"+index+"'>"+value+"</option>";
                    });
                    $('#addlinkto').select2('enable');
                    $('#addlinkto').html(opts);
                    $(this).parents('.form-group').next().find("label").html($(this).find("option:selected").text()+"<span style='color:red;'> *</span>");
                });
            }
     });
        
    // build add newmessage html 
     function addMessageUI(listdata){
        var messagespace = "";
        var objecttype = "";
        var objectval = '<div class="form-group">\n\
                            <label class="col-lg-3 control-label">Linked To <span style="color:red;"> *</span></label>\n\
                            <div class="col-lg-9">\n\
                                <select id="addlinkto" class="select2" disabled>\n\
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
            //console.log(index);
            if(index == "message_type"){
                messagespace += '<div class="form-group">\n\
                            <label class="col-lg-3 control-label">Message Type <span style="color:red;"> *</span></label>\n\
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
                            <label class="col-lg-3 control-label">Object Type <span style="color:red;"> *</span></label>\n\
                            <div class="col-lg-9">\n\
                                <select id="addobject" class="select2"><option></option>'
                                    $.each(value,function(i,v){
                                       objecttype += "<option value='"+i+"'>"+v+"</option>";

                                    });
                objecttype += '</select>\n\
                            </div>\n\
                        </div>'
            }
            
            if(index == "status"){
                states += '<div class="form-group">\n\
                            <label class="col-lg-3 control-label">Status <span style="color:red;"> *</span></label>\n\
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
                            <label class="col-lg-3 control-label">Priority <span style="color:red;"> *</span></label>\n\
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
                            <label class="col-lg-3 control-label">To <span style="color:red;"> *</span></label>\n\
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
     
     // when click edit button get data from database
     function geteditdata(mid,parentnode){
         ajaxcall("msg_list_ajax.php",{action:'edit',id:mid},"post",function(data){
             var editdata = $.parseJSON(data);
             editmload = true;
             $(".child"+mid).find("#loader2").hide();
             if(listdata != null){
                 setupeditdata(editdata,parentnode);
             }
          },function(xhr, ajaxOptions, thrownError){
                $('#loader').hide();
                $('#mloadingerror').show();
          });
     }
     
     function fillopts(optdata,parentsnode){
         loadeditdata(optdata,parentsnode);
         parentsnode.find('.select2').select2({ placeholder : 'Please Select' });
         clearInterval(listsetintervel);
     }
     
     // message remove functionlaty when click trash icon
     $(document).on("click",".trash",function(event){
          event.preventDefault();
          event.stopPropagation();
          var self = $(this);
          swal({
		title: "Are you sure?",
		text: "You will not be able to recover this message!",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'Yes, delete it!',
		closeOnConfirm: true
	},
	function(){
	  self.find("span").removeClass("glyphicon glyphicon-trash").addClass("fa fa-spinner fa-pulse fa-fw");
          ajaxcall("msg_list_ajax.php",{action:'delete',delete_id:[self.parents("tr").data("id")]},"post",function(data){
            if(data == "sucess"){
                deletedmessagelocal(allmessagesobject,filtermessagedata,[self.parents("tr").data("id")]);
                toastr.success('', 'Message deleted successfully');
            }else{
                toastr.error('', 'Error to deleted.');
                self.find("span").removeClass("fa fa-spinner fa-pulse fa-fw").addClass("glyphicon glyphicon-trash");
            }
          },function(xhr, ajaxOptions, thrownError){
                $('#loader').hide();
                $('#mloadingerror').show();
                toastr.error('', 'Error to deleted.');
          });
	});
          
      });
      
      // save message code
      $(document).on("click","#addmessagemodal #savebtn",function(event){
          var self = $(this);
          if($(this).parents(".modal-content").find(".modal-body #addto").select2("val") == "" || $(this).parents(".modal-content").find(".modal-body #addmessagetype").select2("val") == "" || $(this).parents(".modal-content").find(".modal-body #addobject").select2("val") == "" || $(this).parents(".modal-content").find(".modal-body #addlinkto").select2("val") == "" || $(this).parents(".modal-content").find(".modal-body #addpriority").select2("val") == "" || $(this).parents(".modal-content").find(".modal-body #addstatus").select2("val") == ""){
                alert("Please fill all the required fields");
                return;
            }
            
          self.button('loading');
          var saveobj = {}; 
          
          saveobj.assigned_to = $(this).parents(".modal-content").find(".modal-body #addto").select2("val");
          saveobj.Message_type = $(this).parents(".modal-content").find(".modal-body #addmessagetype").select2("val");
          saveobj.obj_type = $(this).parents(".modal-content").find(".modal-body #addobject").select2("val");
          saveobj.linkto = $(this).parents(".modal-content").find(".modal-body #addlinkto").select2("val");
          saveobj.priority = $(this).parents(".modal-content").find(".modal-body #addpriority").select2("val");
          saveobj.Status = $(this).parents(".modal-content").find(".modal-body #addstatus").select2("val");
          saveobj.content = $(this).parents(".modal-content").find(".modal-body .Editor-editor").html();
          saveobj.username = ouruser;
          saveobj.action="savedata";
          ajaxcall("msg_list_ajax.php",saveobj,"post",function(data){
               //console.log(data);
               var newdata = $.parseJSON(data);
               self.button('reset');
               toastr.success('', 'Message send successfully');
               $('#addmessagemodal').modal('hide');
               checkaddnewmessage(allmessagesobject,filtermessagedata,newdata[0],$("#listtype > #label").data("value"),$("#activetype > #label").data("value"))
              
         },function(error){
             self.button('reset');
             toastr.error('', 'Error to send.');
         });
      });
      
      
      //select all code
      $("#allselect").click(function(evt){
         if($(this).prop("checked"))
             $('.messagerow').find('.mail-checkbox').prop("checked",true);
         else
            $('.messagerow').find('.mail-checkbox').prop("checked",false); 
         
      });
      
      // remove selected messages frome view and database
      $('#removeselect').click(function(evt){
            evt.preventDefault();
            var selectelemntdata = [];
            var removeelements = [];
            $('.messagerow').find('.mail-checkbox:checked').each(function(index,element){
                selectelemntdata.push($(this).parents("tr").data("id"));
                removeelements.push(element);
            });
            if(selectelemntdata.length == 0)
                alert("Please select at least one message");
            if(selectelemntdata.length != 0){
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this messages!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'Yes, delete it!',
                        closeOnConfirm: true
                },
                function(){
                    ajaxcall("msg_list_ajax.php",{action:'delete',delete_id:selectelemntdata},"post",function(data){
                      if(data == "sucess"){
                          deletedmessagelocal(allmessagesobject,filtermessagedata,selectelemntdata);
                          toastr.success('', 'delete messages successfully');
                      }else{
                          toastr.error('', 'Error to deleted.');
                      }
                      },function(xhr, ajaxOptions, thrownError){
                            //$('#loader').hide();
                            //$('#mloadingerror').show();
                            toastr.error('', 'Error to deleted.');
                      });
                });
            }
            
      });
      
      // add message view on message list
      function checkaddnewmessage(messageslist,filderdata,newdata,listType,activityType){
          messageslist.unshift(newdata);
          var messagehtml = "";
         if(listType.toLowerCase() == "all" && activityType.toLowerCase() == "all")
            addmessageview()
         else if(listType.toLowerCase() == "all" && activityType.toLowerCase() != "all")
         {
            if(actOrInact($.makeArray(newdata),activityType).length !=0)
                addmessageview()
             
         }
         else if(listType.toLowerCase() != "all" && activityType.toLowerCase() != "all")
         {
            var actdata = searchByUser($.makeArray(newdata),listType.toLowerCase());
            if(actOrInact(actdata,activityType).length !=0)
               addmessageview();
         }
         else
         {
             if(searchByUser($.makeArray(newdata),listType.toLowerCase()).length != 0)
                addmessageview()
             //console.log("activesearch : "+searchByUser($.makeArray(newdata),listType.toLowerCase()));
         }
         
         function addmessageview(){
            filderdata.unshift(newdata);
            messagehtml = messageview($.makeArray(newdata));
            messagecount = filderdata.length;
            $("#totalmassages").html(messagecount);
            //console.log("1 : " + startindex + " 2: "+endindex);
            var lastchildid = $(".table .messagerow").last().data("id");
            if(startindex == 0){
              $(".child"+lastchildid).remove();
              $(".table .messagerow").last().remove();
              $("#messages .table tbody").prepend(messagehtml);
            }else{
                
            }  
            updatemessagecount(messagecount,startindex,endindex);
            npAd(messagecount,endindex,viewcount);
         }
      }
      
      // edit message code
      $(document).on("click",".editmessage",function(event){
          event.preventDefault();
          event.stopPropagation();
          var getid = $(this).parents("tr").data("id");
          if($(this).hasClass("disabled") || $(this).data("disablededit") == true)
              return;
          //alert($(this).parents("tr").next(".child"+getid).css("display"))
          if($(this).parents("tr").next(".child"+getid).css("display") == "table-row"){
                $(this).parents("tr").trigger('click',["edit",false]);
                disabledRootEdit($(this));
          }else{
              $(this).parents("tr").trigger('click',["edit",true]);
                disabledRootEdit($(this));
          }
      });
      
      // code for message selected by checkbox
     $(document).on("click",".mail-checkbox",function(event){
          event.stopPropagation();
          if($(this).parents('tr').hasClass('messagerow')){
              var nitems = $(this).parents('#messages .table tbody').find('.messagerow .mail-checkbox').length;
              var checkedcount = 0;
              $(this).parents('#messages .table tbody').find('.messagerow .mail-checkbox').each(function(index,element){
                  if(!$(this).prop("checked")){
                      $('#allselect').prop("checked",false);
                      return false;
                  }else{
                      checkedcount++;
                      //console.log(nitems + " : " + checkedcount);
                      if(nitems == checkedcount)
                        $('#allselect').prop("checked",true);
                  }
              });
          }
     });
     // editmessage inner
     $(document).on("click",".editm",function(event){
          event.stopPropagation();
          if($(this).data("disablededit") == true)
              return;
          
          $(this).parents("tr").prev('tr').trigger('click',["edit",false]);
          disabledRootEdit($(this).parents('tr').prev('tr').find('.editmessage'));  
      });
      
      //show link tabs
      $(document).on("click",".infocontent a",function(event){
          event.preventDefault();
          var self = $(this);
          var gethref = self.attr("href");
          var tabname = self.text();
          var tabid = tabname.replace(/ /g, "_");
          var matched = null; 
          self.parents(".info .with-nav-tabs .panel-body .tab-content").children().each(function(index,value){
              if(tabid == $(this).attr("id")){
                  self.parents(".info").find('.with-nav-tabs .panel-heading .nav.nav-tabs li').removeClass("active");
                  self.parents(".info .with-nav-tabs .panel-body .tab-content .tab-pane").removeClass("active");
                  self.parents(".info .with-nav-tabs .panel-body .tab-content .tab-pane").removeClass("in");
                  self.parents(".info").find('.with-nav-tabs .panel-heading .nav.nav-tabs li').eq(index).addClass("active");
                  $(this).addClass("active").addClass("in");
                  matched = "match";
                  return;
              }
          });
          if(matched != null)
              return;
          
          self.parents(".info").find('.with-nav-tabs .panel-heading .nav.nav-tabs li').removeClass("active");
          self.parents(".info").find('.with-nav-tabs .panel-heading .nav.nav-tabs').append('<li class="active"><a href="#'+tabid+'" data-toggle="tab">'+tabname+'</a></li>');
          self.parents(".info .with-nav-tabs .panel-body .tab-content .tab-pane").removeClass("active");
          self.parents(".info .with-nav-tabs .panel-body .tab-content").append('<div id="'+tabid+'" class="tab-pane fade in active"><iframe src="'+gethref+'" style="border:0px; width:100%; height:100%;min-height: 320px;"></iframe></div>')
      });
      // prevent message link.
      $(document).on("click",".messagecontent a",function(event){
          event.preventDefault();
      });
      
      //save message code
      $(document).on("click",".savem",function(event){
          event.stopPropagation();
          var getid = $(this).parents("tr").prev("tr").data("id");
          var self = $(this);
          var saveobj = {};
          saveobj.id = getid;
          saveobj.assigned_to = $(this).parents(".info").find(".assignedto").select2("val") == null?"": $(this).parents(".info").find(".assignedto").select2("val")
          saveobj.Message_type = $(this).parents(".info").find(".messagetype").select2("val") == null?"": $(this).parents(".info").find(".messagetype").select2("val")
          saveobj.priority = $(this).parents(".info").find(".priority").select2("val") == null ? "" : $(this).parents(".info").find(".priority").select2("val")
          saveobj.Status = $(this).parents(".info").find(".status").select2("val") == null?"":$(this).parents(".info").find(".status").select2("val")
          if($(this).parents(".info").find(".medit .Editor-editor").text().trim() != ""){
            //$(this).parents(".info").find(".medit .Editor-editor").find("br").last().remove();
            saveobj.content = $(this).parents(".info").find(".medit .Editor-editor").html();
            //console.log(saveobj.content)
          }
          saveobj.username = ouruser;
          saveobj.action="savedata";
          if($(this).parents(".info").find(".status").select2("val") == "done"){
              $("#donealert").data("objectType",[$(this).parents(".info").find(".objecttype").select2("data").id,$(this).parents(".info").find(".objecttype").select2("data").text]);
              $("#donealert").data("linkTto",[$(this).parents(".info").find(".linkedto").select2("data").id,$(this).parents(".info").find(".linkedto").select2("data").text]);
              $("#donealert").data("savedata",JSON.stringify(saveobj));
              $("#donealert").data("targetid",".savem");
              $("#donealert").data("movefileid",getid)
              $("#donealert").modal("show");
          }else{
              self.button('loading');
              saveEditMessageData(saveobj,self,getid)
          }
      });
      
      // ajxcall for save edit message data
      
      function saveEditMessageData(messagedata,self,getid){
          ajaxcall("msg_list_ajax.php",messagedata,"post",function(data){
               //console.log(data);
               var saveddata = $.parseJSON(data);
               self.button('reset');
               toastr.success('', 'Message save successfully');
               self.parents(".info").find(".editm").show();
               self.parents(".info").find(".savem").hide();
               updateallmdata(allmessagesobject,saveddata,getid);
               
               if(messagestyremove(saveddata,$("#listtype > #label").data("value"),$("#activetype > #label").data("value")) == "remove"){
                    updatefdata(filtermessagedata,saveddata,getid)
                    self.parents('tr').prev().remove()
                    self.parents('tr').remove();
                    messagecount = filtermessagedata.length;
                    $("#totalmassages").html(messagecount);
                    //console.log("1 : " + startindex + " 2: "+endindex);
                    updatemessagecount(messagecount,startindex,endindex);
                    npAd(messagecount,endindex,viewcount);

                    if(endindex <= messagecount && messagecount !=0){
                       var appendobj = [];
                       appendobj.push(filtermessagedata[((endindex+1)-1)]);
                       var messagehtml = messageview(appendobj);
                       $("#messages .table tbody").append(messagehtml);
                    }else if(startindex == messagecount && messagecount !=0){
                        startindex -= viewcount;
                        showMessages(allmessagesobject,startindex,endindex,startTo);
                    }
               }else{
                   self.parents(".info").find(".medit .Editor-editor").html("");
                   $(".child"+getid).find('.info .minfo').show();
                   $(".child"+getid).find('.info .medit').hide();
                   $.each(saveddata[0],function(index,value){
                        if(index == "assigned_to")
                          $(".child"+getid).find('.infoassigen').html(value);
                        
                        if(index == "Message_type")
                          $(".child"+getid).find('.infomessagetype').html(value);
                      
                        if(index == "date")
                          $(".child"+getid).find('.senddate').html(value);
                      
                        if(index == "priority")
                          $(".child"+getid).find('.infopriority').html(value);
                      
                        if(index == "Status"){
                          $(".child"+getid).find('.infostatus').html(value);
                          $(".child"+getid).prev('tr').find('.messagestatus').html(value);
                          if(value == "done"){
                             $('.editm').attr("data-disablededit",true);
                             $('.editmessage').attr("data-disablededit",true);
                         }
                        }
                        if(index == "content"){
                          var messages = value.split("||");
                          $(".child"+getid).find('.infocontent').html(removeendbr(messages).join("<br>"));
                          $(".child"+getid).prev('tr').find('.messagecontent').html(messages[messages.length-1]);
                        }
                   })
               }
         },function(error){
             self.button('reset');
             toastr.error('', 'Error to save.');
         });
      }
      
      $("#donealert").on("show.bs.modal",function(event){
          //console.log($(this).data("objectType") + " ; linkTto : " + $(this).data("linkTto"))
          $(this).find('#moveobj').html("<option value'"+$(this).data("objectType")[0]+"' selected>"+$(this).data("objectType")[1]+"</option>");
          $(this).find('#movelinkto').html("<option value'"+$(this).data("linkTto")[0]+"' selected>"+$(this).data("linkTto")[1]+"</option>");
          $(this).find('.select2').select2('destroy');
          $(this).find('.select2').select2();
          $("#foldermanager").html("");
          $("#movefile").prop("disabled",true);
          $.ajax({url:"list_subfolders.php",data:{category:$(this).data("objectType")[0],obj_id:$(this).data("linkTto")[0]},type:"post",success: function (data, textStatus, jqXHR) {
                folderTree(data);  
            },error: function (jqXHR, textStatus, errorThrown) {
                toastr.error('', 'Error to loading subfolder.');
            }
        });
          
          //$(this).find("#movefile").data("fileid",$(this).data("movefileid"));
      });
      
      // this event call when message states done that time to click to savemessage
      $(document).on("click","#movefile",function(event){
          var modal = $(this).parents("#donealert");
          var getselectedfolder = modal.find("#tree input[type=radio]:checked").closest("li").data("folderid");
          if(getselectedfolder == undefined){
              alert("Plese select the folder.")
              return;
          }
          $('#movefile').button({loadingText: 'Processing...'});
          $('#movefile').button('loading');
          ajaxcall("file_move.php",{obj_id:modal.data("linkTto")[0],category:modal.data("objectType")[0],msg_id:modal.data("movefileid"),folderid:getselectedfolder},"post",function(data){
              $('#movefile').button('reset');
              if(data == "success"){
                 toastr.success('', 'success');
                 $("#donealert").modal("hide");
                 //console.log("data: " + modal.data("savedata") + "targetid: " + modal.data("targetid"))
                 var saveobj = JSON.parse(modal.data("savedata"));
                 var self = $(modal.data("targetid"));
                 saveEditMessageData(saveobj,self,saveobj['id'])
              }else{
                 toastr.error('', 'Error');
              }
         },ajaxdefaulterrorcallback);
      })
      
      // update all message data
      function updateallmdata(allmessages,newdata,updateid){
          $.each(allmessages,function(index,value){
              if(value.id == updateid){
                  allmessages[index] = newdata[0];
                  return false;
              }
          });
      }
      
      
      function removeendbr(messages){
          var messagesnew = $.map(messages,function(n,i){
            return n.replace(/<br>$/i, '');
        });
        return messagesnew;
      }
      
      function updatefdata(filterdata,newdata,removeid){
          $.each(filterdata,function(index,value){
              if(value.id == removeid){
                  filterdata.splice(index,1);
                  return false;
              }
          });
      }
      
      // when click close message button close viewed message info code
      $(document).on("click",".closeinfo",function(event){
          event.stopPropagation();
          $(this).parents("tr").prev().trigger('click',["close",true]);
      });
      
      // check message state.
      function messagestyremove(saveddata,listType,activityType){
         if(listType.toLowerCase() == "all" && activityType.toLowerCase() == "all")
         {
            return "stay";
         }
         else if(listType.toLowerCase() == "all" && activityType.toLowerCase() != "all")
         {
            if(actOrInact(saveddata,activityType).length !=0)
                return "stay";
            else
                return "remove"; 
         }
         else if(listType.toLowerCase() != "all" && activityType.toLowerCase() != "all")
         {
            var actdata = searchByUser(saveddata,listType.toLowerCase());
            if(actOrInact(actdata,activityType).length !=0)
                return "stay";
            else
                return "remove";    
         }
         else
         {
            if(searchByUser(saveddata,listType.toLowerCase()).length !=0)
                 return "stay";
            else
                return "remove";
         }
      }
      
      // messages preves and next functionlaty 
      $(".prev-btn,.next-btn").click(function(evt){
          if($(this).hasClass('disabled'))
             return;
          if($(this).data('value') == "previous"){
              startindex -= viewcount;
              endindex -= viewcount;
              startTo -= viewcount;
          }else if($(this).data('value') == "next"){
              startindex += viewcount;
              endindex += viewcount;
              startTo += viewcount;
          }
          showMessages(allmessagesobject,startindex,endindex,startTo);
      });
      
      // show messages on UI
      function showMessages(json,startindex,endindex,startTo){
          deletecount = 0;
          filtermessagedata = filterMessages(json,$("#listtype > #label").data("value"),$("#activetype > #label").data("value"));
          messagecount = filtermessagedata.length;
          $("#totalmassages").html(messagecount);
          //console.log("test: " + startindex + " : " + endindex +" : " +startTo)
          $("#messages .table tbody").html(showmessageUI(filtermessagedata,messagecount,startindex,endindex,startTo));
      }
      /* messages = sourse of Array for messages
         messagecount = filter messages  
      **/
      function showmessageUI(messages,totalmessages,startindex,endindex,startTo){
        var messagehtml = "";
        var filtermessages = [];
        var currentmessages = [];
        filtermessages = filtermessages.concat(messages);
        currentmessages = filtermessages.splice(startTo,viewcount);
        //console.log("dsafsdf: "+allmessagesobject + " lenght: " + allmessagesobject.length);
        messagehtml = messageview(currentmessages);
            updatemessagecount(totalmessages,startindex,endindex);
            npAd(totalmessages,endindex,viewcount);
        return messagehtml;   
      }
      
      // single message view
      function messageview(messages){
          
          var messagehtml = ""
          $.each(messages,function(index,value){
                var content = value.content.split("||");
                messagehtml += '<tr class="messagerow" data-id="'+value.id+'">\n\
                                    <td class="cel1">\n\
                                        <input type="checkbox" class="mail-checkbox">\n\
                                    </td>\n\
                                    <td class="cel2">\n\
                                        <div class="cellcontent messagefrom">'+
                                               value.from+
                                        '</div>\n\
                                    </td>\n\
                                    <td class="cel3">\n\
                                        <div class="cellcontent messagelinkto">'+
                                            value.linked_to+
                                        '</div>\n\
                                    </td>\n\
                                    <td class="cel4">\n\
                                        <div class="cellcontent messagecontent">'+
                                             content[content.length-1]+
                                        '</div>\n\
                                    </td>\n\
                                    <td class="cel5">\n\
                                        <div class="cellcontent messagestatus">'+
                                             value.Status+
                                        '</div>\n\
                                    </td>\n\
                                    <td class="cel6">\n\
                                        <div class="pull-left action-buttons">';
                                          if( value.Status == "done"){
                                            messagehtml += '<a href="#" class="editmessage" data-disablededit="true"><span class="glyphicon glyphicon-pencil"></span></a>';
                                          }else{
                                              messagehtml += '<a href="#" class="editmessage" data-disablededit="false"><span class="glyphicon glyphicon-pencil"></span></a>'
                                          }
                                           messagehtml +=  '<a href="#" class="trash"><span class="glyphicon glyphicon-trash"></span></a>\n\
                                            <span class="indicater"><i class="glyphicon glyphicon-chevron-down text-muted"></i></span>\n\
                                        </div>\n\
                                    </td>\n\
                                </tr>';
            });
            return messagehtml;
      }
      
      function redefindchilds(childsobj){
          $.each(childsobj,function(index,value){
              $(value.htmlelement).insertAfter($(".messagerow[data-id='"+value.id+"']"));
          });
      }
      
      // set all options for edit data.
      function loadeditdata(editdata,parentsnode){
          $.each(editdata,function(index,value){
                //console.log(index);
                if(index == "message_type"){
                   $.each(value,function(i,v){
                      parentsnode.find('.messagetype').append("<option value='"+i+"'>"+v+"</option>");
                   });
                }
                
                if(index == "status"){
                   $.each(value,function(i,v){
                       parentsnode.find('.status').append("<option value='"+i+"'>"+v+"</option>");
                   });
                }
                
                if(index == "priority"){
                   $.each(value,function(i,v){
                       parentsnode.find('.priority').append("<option value='"+i+"'>"+v+"</option>");
                   });
                }
                
                if(index == "assigned_to"){
                   $.each(value.users,function(i,v){
                       parentsnode.find('.assignedto').append("<option value='"+i+"'>"+v+"</option>");
                   });
                   parentsnode.find('.assignedto').append('<optgroup label="User Group"></optgroup>')
                   $.each(value.user_group,function(i,v){
                       parentsnode.find('.assignedto optgroup').append("<option value='"+i+"'>"+v+"</option>");
                   });
                }
          });
      }
      
      // set the selected value on select boxs.
      function setupeditdata(resivedata,parentnode){
         $.each(resivedata[0],function(index,value){
             //console.log("assenval: "+index +" : "+value["value"])
             
             if(index == "assigned_to" && value !="")
                 parentnode.find('.assignedto').select2("val", value["value"]);
             
             if(index == "priority" && value !="")
                 parentnode.find('.priority').select2("val", value["value"]);
             
             if(index == "Status" && value !="")
                 parentnode.find('.status').select2("val", value["value"]);
             
             if(index == "Message_type" && value !="")
                 parentnode.find('.messagetype').select2("val", value["value"]);
             
             if(index == "object_type" && value !="")
               parentnode.find('.objecttype').select2('data', {"id": value["value"], "text": value["title"]});
           
             if(index == "linked_to" && value !="")
               parentnode.find('.linkedto').select2('data', {"id": value["value"], "text": value["title"]});

         });
     }
      // disabled root edit button
      function disabledRootEdit(rootchild){
           rootchild.removeClass('enabled').addClass("disabled");
      }
      // enabled root edit button
      function enabledRootEdit(rootchild){
          rootchild.removeClass('disabled').addClass("enabled");
      }
      
      // delete message control
      function deletedmessagelocal(messageslist,filderdata,deletedelements){
          //console.log(messageslist.length + " : " + filderdata.length)
          $.each(deletedelements,function(i,v){
              $.each(messageslist,function(index,value){
                  if(value.id == v){
                     messageslist.splice(index,1);
                     return false;
                  }
              });
              $.each(filderdata,function(index,value){
                  if(value.id == v){
                     deletecount++;
                     filderdata.splice(index,1);
                     $(".table .messagerow[data-id='"+v+"']").remove();
                     $(".child"+v).remove();
                     messagecount = filderdata.length;
                     $("#totalmassages").html(messagecount);
                     //console.log("1 : " + startindex + " 2: "+endindex);
                     updatemessagecount(messagecount,startindex,endindex);
                     npAd(messagecount,endindex,viewcount);
                     if(endindex <= messagecount && messagecount !=0){
                        var appendobj = [];
                        appendobj.push(filderdata[((endindex+deletecount)-1)]);
                        var messagehtml = messageview(appendobj);
                        $("#messages .table tbody").append(messagehtml);
                     }else if(startindex == messagecount && messagecount !=0){
                         startindex -= viewcount;
                         endindex -= viewcount;
                         startTo -= viewcount;     
                         showMessages(allmessagesobject,startindex,endindex,startTo);
                     }
                     return false;
                  }
              });
          });
      }
      
      function getelementdata(messageslist,messageid){
          var messageboj = null;
          $.each(messageslist,function(index,value){
              if(messageid == value.id){
                  messageboj = value
                  return false;
              }
                  
          });
          return messageboj;
      }
      
      // disabled and enabled next and back buttons.
      function npAd(total,current,viewcount){
          //console.log("total : " + total + " current : " + current +  " viewcount : " + viewcount);
          if(total <= viewcount){
              $(".next-btn").removeClass("enabled").addClass("disabled");
              $(".prev-btn").removeClass("enabled").addClass("disabled");
          }else if(total >  current && current <= viewcount){
              $(".next-btn").removeClass("disabled").addClass("enabled");
              $(".prev-btn").removeClass("enabled").addClass("disabled");
          }else if(total >  current && current > viewcount){
              $(".next-btn").removeClass("disabled").addClass("enabled");
              $(".prev-btn").removeClass("disabled").addClass("enabled");
          }else if(total <=  current){
              $(".next-btn").removeClass("enabled").addClass("disabled");
              $(".prev-btn").removeClass("disabled").addClass("enabled");
          }/*else if(total == current){
              $(".next-btn").removeClass("enabled").addClass("disabled");
          } else if(viewcount == current){
             $(".prev-btn").removeClass("enabled").addClass("disabled");
          }*/     
      }
      
      // update message count
      function updatemessagecount(total,startindex,endindex){
          if(endindex > total)
             endindex = (endindex - (endindex - total));
          $("#messagectext").html((startindex+1)+"-"+endindex + " of "+total);
      }
      
      
      // filter messages code
     function filterMessages(allmessagesobject,listType,activityType){
         var messages = null;
         //console.log(allmessagesobject + " : " + listType + " : " + activityType);
         if(listType.toLowerCase() == "all" && activityType.toLowerCase() == "all")
         {
            messages = $.grep(allmessagesobject,function(e, i){return true});
         }
         else if(listType.toLowerCase() == "all" && activityType.toLowerCase() != "all")
         {
            messages = actOrInact(allmessagesobject,activityType);
         }
         else if(listType.toLowerCase() != "all" && activityType.toLowerCase() != "all")
         {
            var actdata = searchByUser(allmessagesobject,listType.toLowerCase());
            messages = actOrInact(actdata,activityType);
         }
         else
         {
             messages = searchByUser(allmessagesobject,listType.toLowerCase());
         }
         return messages;
     }
     
     // get option data from database.
     function getoptdata(){
         ajaxcall("msg_list_ajax.php",{action:'listdata'},"post",function(data){
             listdata = $.parseJSON(data);
             $("#loader2").hide();
         },ajaxdefaulterrorcallback);
     }
     
     // find active and inactive messages.
     function actOrInact(data, type){
         var searchby = type.split(",");
         //console.log(data);
         var filterdata = $.grep(data,function(e, i){
             if(searchby[0] == "active"){
                 //console.log("active: " + ($.inArray(e.Status.toLowerCase(),searchby)==-1 && $.inArray(e.activity,searchby)!=-1));
                return ($.inArray(e.Status.toLowerCase(),searchby)==-1 && $.inArray(e.activity,searchby)!=-1) 
            }else if(searchby[0] == "inactive"){
                //console.log("inactive: " + ($.inArray(e.Status.toLowerCase(),searchby)!=-1 && $.inArray(e.activity,searchby)!=-1));
                return ($.inArray(e.Status.toLowerCase(),searchby)!=-1 && $.inArray(e.activity,searchby)!=-1)
            }
         })
         return filterdata;
     }
     
     // search by user messages
     function searchByUser(data,user){
         //console.log(JSON.stringify(data) + " : " + user)
          var userdata = $.grep(data,function(e, i){
              return (e.assigned_user.toLowerCase() == user.toLowerCase());
          });
         return userdata;
     }
     // reset message counts
     function restcounts(){
         viewcount=10, startindex=0, endindex = viewcount, startTo=0;
     }
     
     // split screen ui
     function splitpenal(reminesize,$topcontenar,$bottomcontenar){
         var body = (document.body.clientHeight - reminesize);
         var toppenal = (body*60)/100;
         var bottompenal = (body*40)/100;
         $topcontenar.css("height",toppenal+"px");
         $bottomcontenar.css("height",bottompenal+"px");
         $(".panel-top").resizable({
            containment: ".panel-container-vertical",
            handleSelector: ".splitter-horizontal",
            resizeWidth: false,
            touchActionNone: false,
            onDrag: function(e, $el, newWidth, newHeight, opt) {
                if (newHeight > body)
                    newHeight = body;
                
                $el.height(newHeight);
                var ourbodyheight = body;
                
                $bottomcontenar.css("height",(ourbodyheight-newHeight)+"px");
                return false;
              }
          });
     }
     
    function folderTree(branchs){
        var barnchdata = $.parseJSON(branchs);                
        var email = barnchdata['email'];
        delete  barnchdata['email'];
        var treebranch = '<ul id="tree">';
        $.each(barnchdata,function(index,value){
            treebranch +='<li data-folderid='+value['id']+'><a href="#">'+value["name"]+'</a>  <input type="radio" name="movefile"><ul></ul></li>';
        });
        treebranch +="</ul>";
        $("#foldermanager").html(treebranch);
        if($.isEmptyObject(barnchdata))
            $("#foldermanager").html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No subfolder this user.");
        else
            $("#movefile").prop("disabled",false);
        
        $('#tree').treed({openedClass : 'glyphicon-folder-open', closedClass : 'glyphicon-folder-close',subfolderurl:listfolders+email});
    }
     
});