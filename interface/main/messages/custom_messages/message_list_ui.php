<?php
require_once("../../../globals.php");
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://'; 
$foulderdata = $protocol . $_SERVER['HTTP_HOST'] . '/api/DriveSync/listallfiles/';
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="./css/font-awesome.min.css">
        <link rel="stylesheet" href="../../../../library/customselect/css/select2.css"/>
        <link rel="stylesheet" href="../../../../library/customselect/css/select2-bootstrap.css"/>
        <link rel="stylesheet" href="./css/toastr.css">
        <link rel="stylesheet" href="./css/editor.css">
        <link rel="stylesheet" href="./css/jquery-ui.css">
        <link rel="stylesheet" href="./css/sweetalert.css">
        <link rel="stylesheet" href="./css/custommsbox.css"/>
        <script src="../../../../library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
        <script src="./js/jquery-resizable.js"></script>
        <script src="../../../../library/customselect/js/select2.js"></script>
        <script src="./js/bootstrap.min.js"></script>
        <script src="./js/toastr.js"></script>
        <script src="./js/editor.js"></script>
        <script src="./js/sweetalert.min.js"></script>
        <script src="./js/messagebox.js"></script>
        <script>
            var ouruser = '<?php echo $_SESSION['authUser']; ?>';
            var listfolders ='<?php echo $foulderdata ?>';
        </script>
    </head>
    <body style="background-color:#F0F8FF">
        <div class="wrapper">
            <div class="container-fluid">
                <div class="mail-box">
                   <aside class="sm-side">
                       <div class="user-head">
                           <a class="inbox-avatar" href="javascript:;">
                               <img  width="44" hieght="40" src="./images/user_placeholder.png">
                           </a>
                           <div class="user-name">
                               <h4><a href="#"><?php echo $_SESSION['authUser']; ?></a></h4>
                           </div>
<!--                           <a class="mail-dropdown pull-right" href="javascript:;">
                               <i class="fa fa-chevron-down"></i>
                           </a>-->
                       </div>
                       <div class="inbox-body">
                           <a data-toggle="modal"  title="Compose" data-toggle="modal" data-target="#addmessagemodal" class="btn btn-compose">
                               Add New Message
                           </a>
                           <!-- Modal -->
                           <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="addmessagemodal" class="modal fade" style="display: none;">
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
                                            <button type="button" class="btn btn-send btn-sm" id="savebtn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Sending Message">Send</button>
                                       </div>
                                   </div><!-- /.modal-content -->
                               </div><!-- /.modal-dialog -->
                           </div><!-- /.modal -->
                       </div>
                       <ul class="inbox-nav inbox-divider">
                           <li class="active">
                               <a href="#"><i class="fa fa-envelope-o"></i> Messages <span id="totalmassages" class="label label-danger pull-right"></span></a>
                           </li>
                           <li>
                               <a href="#" id="removeselect"><i class=" fa fa-trash-o"></i> Remove Selected</a>
                           </li>
                       </ul>
                   </aside>
                   <aside class="lg-side">
                       <div class="inbox-head">
                                <div id="menu">
                                    <svg version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="96px" height="77px" viewBox="0 0 96 77" style="enable-background:new 0 0 96 77;" xml:space="preserve"> <path fill-rule="evenodd" d="M90.000,77.000 C90.000,77.000 6.000,77.000 6.000,77.000 C2.686,77.000 0.000,74.314 0.000,71.000 C0.000,71.000 0.000,6.000 0.000,6.000 C0.000,2.686 2.686,-0.000 6.000,-0.000 C6.000,-0.000 90.000,-0.000 90.000,-0.000 C93.314,-0.000 96.000,2.686 96.000,6.000 C96.000,6.000 96.000,71.000 96.000,71.000 C96.000,74.314 93.314,77.000 90.000,77.000 ZM32.000,4.000 C32.000,4.000 7.000,4.000 7.000,4.000 C5.343,4.000 4.000,5.343 4.000,7.000 C4.000,7.000 4.000,70.000 4.000,70.000 C4.000,71.657 5.343,73.000 7.000,73.000 C7.000,73.000 32.000,73.000 32.000,73.000 C32.000,73.000 32.000,4.000 32.000,4.000 ZM92.000,7.000 C92.000,5.343 90.657,4.000 89.000,4.000 C89.000,4.000 36.000,4.000 36.000,4.000 C36.000,4.000 36.000,73.000 36.000,73.000 C36.000,73.000 89.000,73.000 89.000,73.000 C90.657,73.000 92.000,71.657 92.000,70.000 C92.000,70.000 92.000,7.000 92.000,7.000 ZM24.000,19.000 C24.000,19.000 11.000,19.000 11.000,19.000 C11.000,19.000 11.000,13.000 11.000,13.000 C11.000,13.000 24.000,13.000 24.000,13.000 C24.000,13.000 24.000,19.000 24.000,19.000 ZM24.000,32.000 C24.000,32.000 11.000,32.000 11.000,32.000 C11.000,32.000 11.000,26.000 11.000,26.000 C11.000,26.000 24.000,26.000 24.000,26.000 C24.000,26.000 24.000,32.000 24.000,32.000 ZM24.000,45.000 C24.000,45.000 11.000,45.000 11.000,45.000 C11.000,45.000 11.000,39.000 11.000,39.000 C11.000,39.000 24.000,39.000 24.000,39.000 C24.000,39.000 24.000,45.000 24.000,45.000 Z"/> </svg>
                                    Menu 
                                </div>
                                <h3>Messages List</h3>
                           <form action="#" class="pull-right position">
                               <div class="input-append">
                                   <input type="text" class="sr-input" placeholder="Search Message">
                                   <button class="btn sr-btn" type="button"><i class="fa fa-search"></i></button>
                               </div>
                           </form>
                       </div>
                       <div class="inbox-body panel-container-vertical" id="inbox-body">
                           <div class="panel-top">
                                <div class="mail-option">
                                    <div class="btn-group hidden-phone bs-dropdown-to-select-group">
                                        <a id="listtype" data-toggle="dropdown" href="#" class="btn mini blue" aria-expanded="false">
                                            <span id="label" data-value="<?php echo $_SESSION['authUser']; ?>">Just Mine</span>
                                            <i class="fa fa-angle-down "></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" data-value="All">See All</a></li>
                                            <li><a href="#" data-value="<?php echo $_SESSION['authUser']; ?>">Just Mine</a></li>
                                        </ul>
                                    </div>
                                    <div class="btn-group bs-dropdown-to-select-group" data-toggle="tooltip" data-placement="top" title=" Active/InActive">
                                        <a data-toggle="dropdown" id="activetype" href="#" class="btn mini blue">
                                            <span id="label" data-value="All" >Show All</span>
                                            <i class="fa fa-angle-down "></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" data-value="All">Show All</a></li>
                                            <li><a href="#" data-value="active,done,1">Show Active</a></li>
                                            <li><a href="#" data-value="inactive,done,1">Show Inactive</a></li>
                                        </ul>
                                    </div>

                                    <ul class="unstyled inbox-pagination">
                                        <li><span id="messagectext"></span></li>
                                        <li>
                                            <a class="prev-btn disabled" data-value="previous" href="#" data-toggle="tooltip" data-placement="top" title=" Previous"><i class="fa fa-angle-left  pagination-left"></i></a>
                                        </li>
                                        <li>
                                            <a class="next-btn disabled" data-value = "next" href="#" data-toggle="tooltip" data-placement="top" title=" Next"><i class="fa fa-angle-right pagination-right"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="messages">
                                     <table class="table table-inbox table-hover">
                                         <thead>
                                             <tr>
                                                 <th class="cel1">
                                                      <input id="allselect" type="checkbox" class="mail-checkbox">
                                                 </th>
                                                 <th class="cel2">
                                                      <div class="cellcontent">From</div>
                                                 </th>
                                                 <th class="cel3">
                                                      <div class="cellcontent">Linked To</div>
                                                 </th>
                                                 <th class="cel4">
                                                      <div class="cellcontent">Content</div>
                                                 </th>
                                                 <th class="cel5">
                                                      <div class="cellcontent">Status</div>
                                                 </th>
                                                 <th class="cel6">
                                                      <div class="cellcontent">Action</div>
                                                 </th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             <tr class="">
                                                 <td colspan="6">
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
                                                         <div id="loadertitle">Messages Loading...</div>
                                                     </div>
                                                     <div class="mloadingerror"> Messages Loading error!!</div>
                                                 </td>
                                             </tr>
                                         </tbody>
                                     </table>
                                </div>
                           </div>
                           <div class="splitter-horizontal">
                            </div>
                           <div class="panel-bottom">
                                bottom panel
                            </div>
                       </div>
                   </aside>
                </div>
            </div>
        </div>
        
        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="linkmodal" class="modal fade" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add Hyperlink</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-3 col-md-2">
                                    <label>Title</label>
                                </div>
                                <div class="col-xs-9 col-md-10">
                                    <div class="required-field-block">
                                        <input type="text"  name="hypertitle" id="hypertitle" placeholder="Enter link title" class="form-control">
                                        <div class="required-icon">
                                            <div class="text">*</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-3 col-md-2">
                                    <label>URL</label>
                                </div>
                                <div class="col-xs-9 col-md-10">
                                    <div class="required-field-block">
                                        <input type="text" id="hyperurl" name="hyperurl" placeholder="Enter link start with http:// or https://" class="form-control">
                                        <div class="required-icon">
                                            <div class="text">*</div>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="linksubmit">Submit</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <div class="modal fade" name = "donealert" id="donealert" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="vertical-alignment-helper">
                <div class="modal-dialog vertical-align-center modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                             <h4 class="modal-title" id="myModalLabel">Move to Drive</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <div class="col-sm-4">
                                    <label>Object Type</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select class="select2" id="moveobj" disabled="">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                 <div class="form-group">
                                     <div class="col-sm-4">
                                        <label>Linked To</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select class="select2" id="movelinkto" disabled="">
                                            <option></option>
                                        </select>
                                    </div>
                                 </div>
                            </form>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label><sup style="color:red">*</sup>Please Select the folder for move/copy linked files</label>
                                </div>
                            </div>
                            <div id="foldermanager">
                                
                            </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="movefile">File Move</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
