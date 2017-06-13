<?php
require_once("../../globals.php");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/acl.inc");



?>
<html>
<head>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/css/bootstrap-3.2.0.min.css" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/css/bootstrap-example.css" type="text/css">


<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/bootstrap-3.2.0.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/prettify.js"></script>

<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/css/bootstrap-multiselect.css" type="text/css">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/js/bootstrap-multiselect.js"></script>
<style>
.filter .btn {
    padding: 9px;
}
</style>

<script>
 $(document).ready(function(){
    $('#usertype-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            maxHeight: 250
    });
    $('#userlist-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            maxHeight: 250
    });
    $('#group-type-post').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            maxHeight: 250
    });
    $('#userroles-post').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        maxHeight: 300
    });
    
  
    
    $('#usergroup_mapping').html('loading...');
    $('#usergroup_mapping').load('custom_usergroup_add.php');
    
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
        
        var user_type_name = $('#usertype-post option:selected').text();
        var group_mem_name =[];
        $('#userlist-post option:selected').each(function(index,value){
            if($(value).val().trim() !=""){
                group_mem_name.push($(this).text())
            }
            
        });
        var group_type_name=$('#group-type-post option:selected').text()
        
        
        $.ajax({
                    type: 'POST',
                    url: "custom_usergroup_update.php",	
                    data: {
                            user_type:user_type,                        
                            group_mem:group_mem,
                            group_type:group_type,
                            gname:gname,
                            role:role,
                         },
                    success: function(response)
                    {
                       window.parent.updatedatatable([group_type_name,user_type_name,group_mem_name.join(","),gname]);
                    },
                    failure: function(response)
                    {
                            alert("error");
                    }		
            });
        });
 });
 
 function load_user(){
 var type = $('#usertype-post').val();
 var role=  $('#userroles-post').val();

  $.ajax({
        type: 'POST',
        url: "user_folder_view.php",	
        data: {type:type,group:'group',role:role,action:'edit'},
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

</script>    

</head>
<body style="background-color:#FFEBD7; padding:30px;">
 <?php
  if(isset($_GET['id'])):
        $selectQuery = "SELECT * FROM tbl_allcare_usergroup WHERE id = ". $_GET['id'] ." ORDER BY id DESC";
        $Rows = sqlStatement($selectQuery);
            while($row = sqlFetchArray($Rows)):
               $user_type =$row['user_type']; 
               $group_type =$row['group_type']; 
               $group_name =$row['group_name'];
               $group_members=array_filter(unserialize($row['group_members']));
               $user_roles=array_filter(unserialize($row['user_roles']));
               
            endwhile;
    endif;
    
    
   if($user_type=='emr') {    
        $user=sqlStatement("select * from users where username!='' and (fname!='' or lname !='') ");
    }else if($user_type=='patients'){
        $user=sqlStatement("select p.pid as id ,portal_username as username, pd.* from patient_access_onsite p inner join patient_data pd ON p.pid=pd.pid where portal_username!=''");  
    }else if($user_type=='agencies'){
        
        $user=sqlStatement("select uid as id ,portal_username as username,u.* from tbl_allcare_agencyportal a inner join users u on u.id=a.uid where portal_username!=''");   
    }
    $arr_user=[];
    while($urow=sqlFetchArray($user)) {
        $arr=''; $arr=array(); $str='';
        $uname=$urow['username'];
        $name=trim($urow['lname'].", ".$urow['fname'],",");
        $arr_user[$uname]=$name;
    }
   
 ?>
<form class="formHorizontal" id="formHorizontal" name="formHorizontal" method="POST">
    <div class="form-group" >
        <label class="col-sm-2 col-xs-4 control-label">User Type</label>
        <div  class="col-sm-10 col-xs-8">
            <select id="usertype-post" name="user_type" onchange="load_roles();">
                <?php 
                $lsql=sqlStatement("select * from  list_options where list_id='AllcareUserTypes'");
                while($lrow=sqlFetchArray($lsql)){
                    echo "<option value=".$lrow['option_id']; if($user_type==$lrow['option_id']) echo " selected";   echo ">".$lrow['title']."</option>";
                }
                ?>
               
            </select>
        </div>
    </div>
     <div class="form-group" >
        <label  class="col-sm-2 col-xs-4 control-label user-list" >User Roles</label>
        <div class="col-sm-10 col-xs-8 user-list" >
            <select id='userroles-post' name='user_roles[]' multiple="multiple" onchange="load_user();">
                <?php foreach(acl_get_group_title_list() as $id2 => $name2){
                     echo "<option value=$name2"; if(in_array($name2,$user_roles)) echo " selected"; echo ">$name2</option>";
                }?>
            </select>
        </div>
    </div>
    <div class="form-group" >
        <label  class="col-sm-2 col-xs-4 control-label user-list" >Group Members</label>
        <div class="col-sm-10 col-xs-8 user-list" >
            <select id='userlist-post' name='user_list[]' multiple="multiple">
                <?php foreach($arr_user as $id => $name1){
                     echo "<option value=$id"; if(in_array($id,$group_members)) echo " selected"; echo ">$name1</option>";
                }?>
            </select>
        </div>
    </div>
    <div class="form-group" >
        <label class="col-sm-2 col-xs-4 control-label">Group Type</label>
        <div class="col-sm-10 col-xs-8">
            <select id='group-type-post' name='group_type'>   
            <?php $sql=sqlStatement("select * from list_options where list_id='note_type'");
                  while($row=sqlFetchArray($sql)){
                      $id=str_replace(" ","_",$row['option_id']);
                    echo "<option value=$id"; if($group_type==$id) echo " selected"; echo ">".$row['title']."</option>";
                 }
            ?>
            </select>    
        </div>
    </div>
    <div class="form-group" >
        <label class="col-sm-2 col-xs-4 control-label">Group Name</label>
        <div class="col-sm-10 col-xs-8">
            <input type="text" id="gname-post" class="form-control input-sm" name="gname" value="<?php echo $group_name; ?>" disabled/>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10 col-xs-offset-4 col-xs-8">
            <button type="button"  id="save" class="btn btn-default">Submit</button>
        </div>
    </div>
</form>

</body>
</html>
