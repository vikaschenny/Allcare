<?php
require_once("../../globals.php");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/acl.inc");

?>
<html>
<head>

<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/css/bootstrap-3.2.0.min.css" type="text/css">
<link rel='stylesheet' type='text/css' href='<?php echo $GLOBALS['webroot'] ?>/interface/main/css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='<?php echo $GLOBALS['webroot'] ?>/interface/main/css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='<?php echo $GLOBALS['webroot'] ?>/interface/main/css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='<?php echo $GLOBALS['webroot'] ?>/interface/main/css/dataTables.colReorder.css'>
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/css/bootstrap-multiselect.css" type="text/css">

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/bootstrap-3.2.0.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/js/bootstrap-multiselect.js"></script>
<script type='text/javascript' src='<?php echo $GLOBALS['webroot'] ?>/interface/main/js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='<?php echo $GLOBALS['webroot'] ?>/interface/main/js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='<?php echo $GLOBALS['webroot'] ?>/interface/main/js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='<?php echo $GLOBALS['webroot'] ?>/interface/main/js/dataTables.colVis.js'></script>
<style>
.filter .btn {
    padding: 9px;
}
#editusergroup .modal-body{
    padding: 0px;
}
</style>
<script>
 var updatetr = null;
 var datatable = null;
 $(document).ready(function(){
    $('#usertype-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            maxHeight: 250
    });
    
    $('#userlist-post').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        maxHeight: 300
    });
    
    $('#userlist-post').multiselect('disable');
    $('#userroles-post').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        maxHeight: 300
    });
    
    $('#userroles-post').multiselect('disable');        
    $('#group-type-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            maxHeight: 250
    });
    
    $('#usergroup_mapping').html('loading...');
    $('#usergroup_mapping').load('custom_usergroup_add.php',function(){datatable = $('#gridview').dataTable();});
    
    $("#save").click(function(e) {
        e.preventDefault();
        if($('#userlist-post').val() == null || 
               $('#usertype-post').val() == null ||
               $('#group-type-post').val() == null ||
               $('#gname-post').val() == null ){
                 
                      alert("All fields are required");
                      return false;
              }
        var user_type=$('#usertype-post').val();
        var group_mem=$('#userlist-post').val();
        var group_type=$('#group-type-post').val();
        var gname=$('#gname-post').val();
        var role=$('#userroles-post').val();
         $('#usergroup_mapping').html('loading...');
            $.ajax({
                        type: 'POST',
                        url: "custom_usergroup_add.php",	
                        data: {
                                user_type:user_type,                        
                                group_mem:group_mem,
                                group_type:group_type,
                                gname:gname,
                                role:role,
                             },
                        success: function(response)
                        {
                            jQuery('#usergroup_mapping').html(response);
                            datatable = $('#gridview').dataTable();

                        },
                        failure: function(response)
                        {
                                alert("error");
                        }		
                });
             document.getElementById("formHorizontal").reset();
        });
        $('#editusergroup').on("show.bs.modal", function(event){
                var target = $(event.relatedTarget);
                var modal = $(this);
                updatetr = target.parents('tr');
                var url = "custom_usergroup_edit.php?id="+target.data("href");
                modal.find(".modal-body").html("<iframe src='"+url+"' style='border:none; width:100%; height:350px'></iframe>"); 
        });
 });
 
 function load_user(){
 var type = $('#usertype-post').val();
 var role=  $('#userroles-post').val();
  $.ajax({
        type: 'POST',
        url: "user_folder_view.php",	
        data: {type:type,group:'group',role:role},
        success: function(response){
           
            var users = JSON.parse(response);
            var options = [];
            $.each(users,function(id,username){
                options.push({label: username, title: username, value: id,})
            });
            if(users.length!=0){
                $('#userlist-post').multiselect('enable');  
                $('#userlist-post').multiselect('dataprovider', options);
            }
        },
        failure: function(response){
           alert("error");
        }		
    }); 
 }
 function load_roles(){
   var type = $('#usertype-post').val();
   if(type=='emr'){
        var response='<?php echo json_encode(acl_get_group_title_list()); ?>';
        var users = JSON.parse(response);
           // var users = JSON.parse(response);
            var options = [];
            $.each(users,function(id,username){
                options.push({label: username, title: username, value: username,})
            });
            
          
            $('#userroles-post').multiselect('enable');  
            $('#userroles-post').multiselect('dataprovider', options);
   }else{
        $('#userroles-post').multiselect('disable');  
       load_user();
   }
   
 }

 function updatedatatable(rowdata){
    var table = $('#gridview').DataTable();
    var data = table
        .row(updatetr)
        .data();
        $.each(rowdata,function(index,value){
            data[index] = value;
        })
        table
        .row( updatetr )
        .data( data )
        .draw();
        
 }
 
 function deletedatatablerow(element){
    var table = $('#gridview').DataTable();
        table
        .row(element.parents('tr'))
        .remove()
        .draw();
        
 }
 
</script>    

</head>
<body style="background-color:#FFEBD7; padding:30px;" class="container">
 
<form class="formHorizontal" name="formHorizontal" id="formHorizontal" method="POST">
    <div class="form-group" >
        <div class="row">
            <label class="col-sm-2 control-label">User Type</label>
            <div  class="col-sm-4">
                <select id="usertype-post" name="user_type" onchange="load_roles();">
                    <option value="">none selected</option>
                    <?php 
                    $lsql=sqlStatement("select * from  list_options where list_id='AllcareUserTypes'");
                    while($lrow=sqlFetchArray($lsql)){
                        echo "<option value=".$lrow['option_id'].">"; echo $lrow['title']."</option>";
                    }
                    ?>
                </select>
            </div>
            <label  class="col-sm-2 control-label user-list">User Roles</label>
            <div class="col-sm-4 user-list">
                <select id='userroles-post' name='user_roles[]' multiple="multiple" onchange="load_user();"></select> 
            </div>
        </div>
    </div>
    <div class="form-group" >
        <div class="row">
            <label  class="col-sm-2 control-label user-list">Group Members</label>
            <div class="col-sm-4 user-list">
                <select id='userlist-post' name='user_list[]' multiple="multiple"></select> 
            </div>
            <label class="col-sm-2 control-label">Group Type</label>
            <div class="col-sm-4">
                <select id='group-type-post' name='group_type'>
                    <option value="">none selected</option>
                <?php $sql=sqlStatement("select * from list_options where list_id='note_type'");
                      while($row=sqlFetchArray($sql)){
                          $id=str_replace(" ","_",$row['option_id']);
                         echo "<option value=".$id.">".$row['title']."</option>";
                     }
                ?>
                </select>    
            </div>
        </div>
    </div>
    <div class="form-group" >
        <div class="row">
            <label class="col-sm-2 control-label">Group Name</label>
                <div class="col-sm-4">
                    <input type="text" id="gname-post" class="form-control input-sm" name="gname" value=""/>
                </div>
        </div>
    </div>    
    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-7">
            <button type="button" id="save" class="btn btn-default">Submit</button>
        </div>
    </div>
</form>
    <div id="usergroup_mapping"></div>
    
<div class="modal fade" name = "edit" id="editusergroup" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Edit User Group Maping</h4>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
    </div>
    
</body>
</html>
