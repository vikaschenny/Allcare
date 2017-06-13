<?php
require_once("../../../globals.php");

if($_POST['deleteid'] != ""){
        $deleteQuery = "DELETE FROM tbl_allcare_document_mapping WHERE id = ".$_POST['deleteid'];
        sqlStatement($deleteQuery);
}elseif($_POST['tkt_type']!=''){
    $data=sqlStatement("select * from tbl_allcare_document_mapping where tkt_type='".$_POST['tkt_type']."'");
    $drow=sqlFetchArray($data);
    if(empty($drow)){
        $sql=sqlStatement("insert into tbl_allcare_document_mapping (date,user,tkt_owner,tkt_type,tkt_priority,tkt_status) values (NOW(),'".$_SESSION['authUser']."','".$_POST['tkt_owner']."','".$_POST['tkt_type']."','".$_POST['tkt_priority']."','".$_POST['tkt_status']."')");
    }else{ 
        if($_REQUEST['update']!=''){
            $sql=sqlStatement("update tbl_allcare_document_mapping set date=NOW(),user='".$_SESSION['authUser']."',tkt_owner='".$_POST['tkt_owner']."',tkt_priority='".$_POST['tkt_priority']."',tkt_status='".$_POST['tkt_status']."' where tkt_type='".$_POST['tkt_type']."'");
        }else {
             echo "update";
             exit();
        }
       

    }
}

?>
<html>
    <head>
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/css/bootstrap-3.2.0.min.css" type="text/css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/css/bootstrap-multiselect.css" type="text/css">
        <link rel='stylesheet' type='text/css' href='<?php echo $GLOBALS['webroot'] ?>/interface/main/css/jquery.dataTables.css'>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/bootstrap-3.2.0.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/js/bootstrap-multiselect.js"></script>
        <script type='text/javascript' src='<?php echo $GLOBALS['webroot'] ?>/interface/main/js/jquery.dataTables.min.js'></script>
        <script>
            $(document).ready(function(){
                
                $('#tkt_owner').multiselect({
                        includeSelectAllOption: true,
                        enableFiltering: true,
                        maxHeight: 250
                });
                $('#tkt_type').multiselect({
                        includeSelectAllOption: true,
                        enableFiltering: true,
                        maxHeight: 250
                });
                $('#tkt_priority').multiselect({
                        includeSelectAllOption: true,
                        enableFiltering: true,
                        maxHeight: 250
                });
                $('#tkt_product').multiselect({
                        includeSelectAllOption: true,
                        enableFiltering: true,
                        maxHeight: 250
                });
                $('#tkt_status').multiselect({
                        includeSelectAllOption: true,
                        enableFiltering: true,
                        maxHeight: 250
                });
                 $('#editusergroup').on("show.bs.modal", function(event){
                    var target = $(event.relatedTarget);
                    var modal = $(this);
                    updatetr = target.parents('tr');
                    var url = "allcare_doc_type_edit.php?id="+target.data("href");
                    modal.find(".modal-body").html("<iframe src='"+url+"' style='border:none; width:100%; height:350px'></iframe>"); 
                });
                
                $("#save").click(function(e) {
                    
                        e.preventDefault();
                        if(($('#tkt_owner').val() == null || $('#tkt_owner').val() == "")){
                            alert("Ticket owner field required");
                            return false;
                        }else if(($('#tkt_type').val() == null || $('#tkt_type').val() == "")){
                            alert("Ticket type field required");
                            return false;
                        }else if(($('#tkt_priority').val() == null || $('#tkt_priority').val() == "")){
                            alert("Ticket priority field required");
                            return false;
                        }else if(($('#tkt_status').val() == null || $('#tkt_status').val() == "")){
                            alert("Ticket status field required");
                            return false;
                        }else{
                            var tkt_owner=$('#tkt_owner').val();
                            var tkt_type=$('#tkt_type').val();
                            var tkt_priority=$('#tkt_priority').val();
                            var tkt_status=$('#tkt_status').val();
                        }
                        
                       
                         $('#usergroup_mapping').html('loading...');
                        $.ajax({
                                    type: 'POST',
                                    url: "allcare_doc_type_mapping.php",	
                                    data: {
                                            tkt_owner:tkt_owner,
                                            tkt_type:tkt_type,
                                            tkt_priority:tkt_priority,
                                            tkt_status:tkt_status,
                                         },
                                    success: function(response)
                                    {
                                           if(response=='update'){
                                                if(confirm("Do you want to update data")==true){
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: "allcare_doc_type_mapping.php",	
                                                        data: {
                                                                tkt_owner:tkt_owner,
                                                                tkt_type:tkt_type,
                                                                tkt_priority:tkt_priority,
                                                                tkt_status:tkt_status,
                                                                update:1
                                                             },
                                                        success: function(response)
                                                        {
                                                            window.location.reload();
                                                        },
                                                        failure: function(response)
                                                        {
                                                                alert("error");
                                                        }		
                                                    });
                                                }else {
                                                   window.location.reload();
                                                }
                                            }else{
                                             window.location.reload();
                                           }
                                    },
                                    failure: function(response)
                                    {
                                            alert("error");
                                    }		
                            });
                     document.getElementById("foldersMapping").reset();
                });
            });
            
            function deletedatatablerow(element){
                var table = $('#gridview').DataTable();
                    table
                    .row(element.parents('tr'))
                    .remove()
                    .draw();

            }
            function editrow(){
                $('#editusergroup').modal('hide'); 
            }
            function del(element,id,event){
                event.preventDefault();
                if (confirm("Do you really want to delete this group") == true) {
                    $.ajax({
                        type: 'POST',
                        url: "allcare_doc_type_edit.php",	
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
        </script>    
    </head>
    <body style="background-color:#FFEBD7; padding:30px;" class="container">
        <form class="foldersMapping" name="foldersMapping" id="foldersMapping"  action="" method="POST">
             <div class="form-group" >
                <div class="row">
                    <label class="col-sm-2 control-label">Ticket Type</label>
                    <div  class="col-sm-4">
                        <select name="tkt_type" id="tkt_type">
                            <option value="">please Select</option>
                            <?php
                             $sql=sqlStatement("select * from list_options where list_id='AllcareCustomMsgType'");
                             while($data=sqlFetchArray($sql)){
                                 echo "<option value=".$data['option_id'];  if($row['title']==$data['option_id'])  echo " selected"; echo ">".$data['title']."</option>";
                             }
                            ?>
                        </select>
                    </div>
                    <label  class="col-sm-2 control-label user-list">Ticket owner</label>
                    <div class="col-sm-4 user-list">
                        <select name="tkt_owner" id="tkt_owner">
                            <option value="">please Select</option>
                            <?php
                             $sqlpr=sqlStatement("select * from users  where username!='' and (fname!='' or lname!='')");
                             while($datapr=sqlFetchArray($sqlpr)){
                                $fullname= $datapr['fname'].",".$datapr['lname'];
                                 echo "<option value=".$datapr['username']; if($datapr['username']==$tkt_owner){ echo " selected"; } echo ">".$fullname."</option>";
                             } 
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group" >
                <div class="row">
                    <label  class="col-sm-2 control-label user-list">Ticket priority</label>
                    <div class="col-sm-4 user-list">
                        <select name="tkt_priority" id="tkt_priority">
                            <option value="">please Select</option>
                            <?php
                             $sqlpr=sqlStatement("select * from list_options where list_id='AllcareCustomMsgPriority'");
                             while($datapr=sqlFetchArray($sqlpr)){
                                 echo "<option value=".$datapr['option_id']; if($datapr['option_id']==str_replace("_"," ",$row['priority'])){ echo " selected"; } echo ">".$datapr['title']."</option>";
                             } 
                            ?>
                        </select>
                    </div>
                     <label  class="col-sm-2 control-label user-list">Ticket Status</label>
                    <div class="col-sm-4 user-list">
                        <select name="tkt_status" id="tkt_status">
                            <option value="">please Select</option>
                            <?php
                             $sql_status=sqlStatement("select * from list_options where list_id='AllcareCustomMsgStatus'");
                             while($data_status=sqlFetchArray($sql_status)){
                                 echo "<option value=".$data_status['option_id']; if($data_status['option_id']==$row['message_status']){ echo " selected"; } echo ">".$data_status['title']."</option>";
                             }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-5 col-sm-7">
                    <button type="button" id="save" class="btn btn-default">Submit</button>
<!--                        <input type="submit" name="save1" id="save1" value="submit"/>-->
                </div>
            </div>
        </form>
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
        <table id="gridview" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Ticket owner</th>
            <th>Ticket type</th>
            <th>Ticket priority</th>
            <th>Ticket status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tfoot>
       <tr>
            <th>Ticket owner</th>
            <th>Ticket type</th>
            <th>Ticket priority</th>
            <th>Ticket status</th>
            <th>Action</th>
        </tr>
    </tfoot>
    <tbody>
        <?php
            $sql1=sqlStatement("select * from tbl_allcare_document_mapping");
            while($row = sqlFetchArray($sql1)):
                $tkt_owner=$row['tkt_owner'];  
                $tkt_type=$row['tkt_type'];   
                $tkt_priority = $row['tkt_priority'];
                $tkt_status = $row['tkt_status'];
               
                
               //to get group type
                $list1=sqlStatement("select * from users where username='$tkt_owner'");
                $lrow1=sqlFetchArray($list1);
        ?>
        <tr>
            <td><?php echo $lrow1['fname'].",".$lrow1['lname']; ?></td>
            <td><?php echo $tkt_type; ?></td>
            <td><?php echo $tkt_priority; ?></td>
            <td><?php echo $tkt_status; ?></td>
            <td><a data-toggle='modal' data-target='#editusergroup' data-href="<?php echo $row['id']; ?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;Edit</a>&nbsp;&nbsp;/&nbsp;&nbsp;<a  class="btn btn-primary btn-sm" href="javascript:void" onclick="del(this,<?php echo $row['id']; ?>,event)"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;Delete</a></td>
        </tr>
        <?php
            endwhile;
        ?>
    </tbody>
</table>
    </body>
</html>