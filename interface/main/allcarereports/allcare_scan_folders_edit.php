<?php
require_once("../../globals.php");

if($_POST['drive_folder']!=''){
    $data=sqlStatement("select * from tbl_allcare_scanfolders where scan_folder='".$_POST['drive_folder']."'");
    $drow=sqlFetchArray($data);
    if(empty($drow)){
        $sql=sqlStatement("insert into tbl_allcare_scanfolders (date,user,scan_folder,tkt_owner,tkt_type,tkt_priority,tkt_status) values (NOW(),'".$_SESSION['authUser']."','".$_POST['drive_folder']."','".$_POST['tkt_owner']."','".$_POST['tkt_type']."','".$_POST['tkt_priority']."','".$_POST['tkt_status']."')");
    }else{
        $sql=sqlStatement("update tbl_allcare_scanfolders set date=NOW(),user='".$_SESSION['authUser']."',tkt_owner='".$_POST['tkt_owner']."',tkt_type='".$_POST['tkt_type']."',tkt_priority='".$_POST['tkt_priority']."',tkt_status='".$_POST['tkt_status']."' where scan_folder='".$_POST['drive_folder']."'");
    }
    
}
?>
<html>
    <head>
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/css/bootstrap-3.2.0.min.css" type="text/css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/css/bootstrap-multiselect.css" type="text/css">
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/bootstrap-3.2.0.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/js/bootstrap-multiselect.js"></script>
      
        <script>
            $(document).ready(function(){
                $('#drive_folder').multiselect({
                        includeSelectAllOption: true,
                        enableFiltering: true,
                        maxHeight: 250
                });
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
                    var url = "custom_usergroup_edit.php?id="+target.data("href");
                    modal.find(".modal-body").html("<iframe src='"+url+"' style='border:none; width:100%; height:350px'></iframe>"); 
                });
                
                $("#save").click(function(e) {
                    e.preventDefault();
                         if(($('#drive_folder').val() == null || $('#drive_folder').val() == "")){
                             alert("Drive folder field required");
                             return false;
                        }else if(($('#tkt_owner').val() == null || $('#tkt_owner').val() == "" )){
                            alert("Ticket owner field required");
                             return false;
                        }else if(($('#tkt_type').val() == null || $('#tkt_type').val() == "" )){
                            alert("Ticket type field required");
                             return false;
                        }else if(($('#tkt_priority').val() == null || $('#tkt_priority').val() == "" )){
                            alert("Ticket prioprity field required");
                             return false;
                        }else if(($('#tkt_status').val() == null || $('#tkt_status').val() == "") ){
                            alert("Ticket Status field required");
                            return false;
                        }else {
                            var drive_folder=$('#drive_folder').val();
                            var tkt_owner=$('#tkt_owner').val();
                            var tkt_type=$('#tkt_type').val();
                            var tkt_priority=$('#tkt_priority').val();
                            var tkt_status=$('#tkt_status').val();
                        }
                        //to update in parent window
                        var dfolder = $('#drive_folder option:selected').text();
                        var towner = $('#tkt_owner option:selected').text();
                        var ttype = $('#tkt_type option:selected').text();
                        var tpriority = $('#tkt_priority option:selected').text();
                        var tstatus = $('#tkt_status option:selected').text();
                           
                         
                        $.ajax({
                            type: 'POST',
                            url: "allcare_scan_folders_edit.php",	
                            data: {
                                    drive_folder:drive_folder,                        
                                    tkt_owner:tkt_owner,
                                    tkt_type:tkt_type,
                                    tkt_priority:tkt_priority,
                                    tkt_status:tkt_status,
                                 },
                            success: function(response)
                            {
                                
                                window.parent.updatedatatable([dfolder,towner,ttype,tpriority,tstatus]);
                                window.parent.editrow();
                            },
                            failure: function(response)
                            {
                                    alert("error");
                            }		
                            });
                     document.getElementById("foldersMapping").reset();
                });
            });
            
        </script>    
    </head>
    <body style="background-color:#FFEBD7; padding:30px;" class="container">
        <?php 
            $sql1=sqlStatement("select * from tbl_allcare_scanfolders where id=".$_REQUEST['id']);
            while($row = sqlFetchArray($sql1)):
                $scan_folder=$row['scan_folder'];  
                $tkt_owner=$row['tkt_owner'];  
                $tkt_type=$row['tkt_type'];   
                $tkt_priority = $row['tkt_priority'];
                $tkt_status = $row['tkt_status'];
            endwhile;
        ?>
        <form class="foldersMapping" name="foldersMapping" id="foldersMapping"  action="" method="POST">
             <div class="form-group" >
                <div class="row">
                    <label class="col-xs-4 control-label">Drive Folders</label>
                    <div class="col-xs-8" style="display: inline-block;">
                        <select name="drive_folder" id="drive_folder">
                            <option value="">please Select</option>
                            <?php
                             $sqlpr=sqlStatement("select * from list_options where list_id='AllcareScanDriveFolders'");
                             while($datapr=sqlFetchArray($sqlpr)){
                                 echo "<option value=".$datapr['option_id']; if($datapr['option_id']==$scan_folder){ echo " selected"; } echo ">".$datapr['title']."</option>";
                             } 
                            ?>
                        </select>
                    </div>
                </div>
             </div>
            <div class="form-group" >
                <div class="row">
                    <label  class="col-xs-4 control-label user-list">Ticket owner</label>
                    <div class="col-xs-8" style="display: inline-block;">
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
                    <label class="col-xs-4 control-label">Ticket Type</label>
                    <div class="col-xs-8" style="display: inline-block;">
                        <select name="tkt_type" id="tkt_type">
                            <option value="">please Select</option>
                            <?php
                             $sql=sqlStatement("select * from list_options where list_id='AllcareCustomMsgType'");
                             while($data=sqlFetchArray($sql)){
                                 echo "<option value=".$data['option_id'];  if($data['option_id']==$tkt_type)  echo " selected"; echo ">".$data['title']."</option>";
                             }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group" >
                <div class="row">
                    <label  class="col-xs-4 control-label user-list">Ticket priority</label>
                    <div class="col-xs-8" style="display: inline-block;">
                        <select name="tkt_priority" id="tkt_priority">
                            <option value="">please Select</option>
                            <?php
                             $sqlpr=sqlStatement("select * from list_options where list_id='AllcareCustomMsgPriority'");
                             while($datapr=sqlFetchArray($sqlpr)){
                                 echo "<option value=".$datapr['option_id']; if($datapr['option_id']==$tkt_priority){ echo " selected"; } echo ">".$datapr['title']."</option>";
                             } 
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group" >
                <div class="row">
                    <label  class="col-xs-4 control-label user-list">Ticket Status</label>
                   <div class="col-xs-8" style="display: inline-block;">
                        <select name="tkt_status" id="tkt_status">
                            <option value="">please Select</option>
                            <?php
                             $sql_status=sqlStatement("select * from list_options where list_id='AllcareCustomMsgStatus'");
                             while($data_status=sqlFetchArray($sql_status)){
                                 echo "<option value=".$data_status['option_id']; if($data_status['option_id']==$tkt_status){ echo " selected"; } echo ">".$data_status['title']."</option>";
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
    </body>
</html>