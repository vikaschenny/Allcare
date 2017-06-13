<?php
require_once("../../globals.php");

$type=$_REQUEST['user_type'];
$gmem=serialize(array_filter($_REQUEST['group_mem']));
$gtype=$_REQUEST['group_type'];
$gname=$_REQUEST['gname'];
$roles=serialize(array_filter($_REQUEST['role']));
if($_POST['deleteid'] != ""){
        $deleteQuery = "DELETE FROM tbl_allcare_usergroup WHERE id = ".$_POST['deleteid'];
        sqlStatement($deleteQuery);
}else if($type!=''&& !empty($gmem) && $gtype!='' && $gname!=''){

    $sql=sqlStatement("select * from tbl_allcare_usergroup where group_name='$gname'");
    $cnt=mysql_num_rows($sql);
    if($cnt==0){
        $sqlInsert=sqlStatement("insert into tbl_allcare_usergroup (date,user_type,group_type,group_members,group_name,login_user,user_roles) values (now(),'$type','$gtype','$gmem','$gname','".$_SESSION['authId']."','$roles')");
    }else{
        $sqlUpdate=sqlStatement("update tbl_allcare_usergroup set date=now(),user_type='$type',group_type='$gtype',group_members='$gmem',login_user='".$_SESSION['authId']."',user_roles='$roles' where group_name='$gname'");
    }
}
?>
<script> 
     
     function del(element,id,event){
         event.preventDefault();
        if (confirm("Do you really want to delete this group") == true) {
            jQuery.ajax({
                type: 'POST',
                url: "custom_usergroup_add.php",	
                data: {
                        deleteid:id,                        
                      },

                success: function(response)
                {
                    window.parent.deletedatatablerow($(element))
                },
                failure: function(response)
                {
                        alert("error");
                }		
            });
        } else {
            return false;
        }
        
    }
</script>
<table id="gridview" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Group Type</th>
            <th>User Type</th>
            <th>User Roles</th>
            <th>Group Members</th>
            <th>Group Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tfoot>
       <tr>
            <th>Group Type</th>
            <th>User Type</th>
            <th>User Roles</th>
            <th>Group Members</th>
            <th>Group Name</th>
            <th>Action</th>
        </tr>
    </tfoot>
    <tbody>
        <?php
            $sql1=sqlStatement("select * from tbl_allcare_usergroup");
            while($row = sqlFetchArray($sql1)):
                
                $user_type=$row['user_type'];  
                $group_type=str_replace("_"," ",$row['group_type']);  
                $group_name=$row['group_name'];   
                $grp_mem = array_filter(unserialize($row['group_members']));
                $implodemem = implode("','",$grp_mem);
                $user_role = array_filter(unserialize($row['user_roles']));
                $userrole = implode(",",$user_role);
                
               //to get group type
                $list=sqlStatement("select * from list_options where list_id='note_type' and option_id='$group_type'");
                $lrow=sqlFetchArray($list);
               //to group members name 
                if($implodemem!=''){
                    if($user_type=='emr') {    
                        $user=sqlStatement("select * from users where username!='' and (fname!='' or lname !='' ) and username IN ('$implodemem')");
                    }else if($user_type=='patients'){
                        $user=sqlStatement("select p.pid as id ,portal_username as username , pd.* from patient_access_onsite p inner join patient_data pd ON p.pid=pd.pid where portal_username!='' and portal_username IN ('$implodemem')");  
                    }else if($user_type=='agencies'){
                       
                        $user=sqlStatement("select uid as id ,portal_username as username ,u.* from tbl_allcare_agencyportal a inner join users u on u.id=a.uid where portal_username!='' and username IN ('$implodemem')");   
                    }
                    $user_arr = array(); 
                    for($i=0; $arr=sqlFetchArray($user); $i++) {
                      $user_arr[$i]=trim($arr['fname']." ".$arr['lname'],",");
                    }
                    
                }
        ?>
        <tr>
            <td><?php echo $lrow['title']; ?></td>
            <td><?php echo $user_type; ?></td>
            <td><?php echo $userrole; ?></td>
            <td><?php echo implode(",",$user_arr); ?></td>
            <td><?php echo$group_name; ?></td>
            <td><a data-toggle='modal' data-target='#editusergroup' data-href="<?php echo $row['id']; ?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;Edit</a>&nbsp;&nbsp;/&nbsp;&nbsp;<a  class="btn btn-primary btn-sm" href="javascript:void" onclick="del(this,<?php echo $row['id']; ?>,event)"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;Delete</a></td>
        </tr>
        <?php
            endwhile;
        ?>
    </tbody>
</table>
 