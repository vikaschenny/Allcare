
<style>
    .borderclass{
        border-bottom: 1px #000000 solid; border-right: 1px #000000 solid;
    }
    
</style>

<script>
   
    function deleteRow(id)
    {
      
        var result = confirm("Are you sure to delete Query?");
        if (result==true) 
        {
                  $.ajax({
                type: 'POST',
		url: "queryDelete.php",	
		data: {id:id},
		
		success: function(response)
		{                         
                                    alert(response);     
                                    parent.frames['RTop'].location.reload();
                                    
                     
                    	},
		failure: function(response)
		{
			alert("error"+ response);
		}
                });
        }
      
    }    
     function emailValidation(name)
    {        
        
                $.ajax({
                type: 'POST',
		url: "querynameValidation.php",	
		data: {name:name},
		
		success: function(response)
		{                         
                                    //alert(response);     
                                    
                                    if (response==0)
                                    {
                                        return true;
                                    }
                                    else
                                      {

                                              alert('The Sql Query Name '+name+' already added');	                                        
                                              $('#name').val('');$('#name').focus();
                                              return false;
                                      }
                   

                     
                    	},
		failure: function(response)
		{
			alert("error"+ response);
		}		
        });	   
        
       
        
    }
    
    function showAddMode(flg)
    {
        if(flg=='Y')
        {
            $("#divQueryList").hide();
            $("#divNewQuery").show();
        }
        else
        {
            $("#divNewQuery").hide();
            $("#divQueryList").show();
            
        }
        
    }
    
    function SaveQueryData()
    {
        var finalURL='add_new_queryfields.php';  
        
        var name= $.trim($("#name").val());
        var description= $.trim($("#description").val());
        var querystring= $.trim($("#querystring").val());
        
         if (name === "")
            {
                alert('Please Enter the Name');
                $('#name').focus();
                return false;
            }
            else if (description === "")
            {
                alert('Please Enter the Description');
                $('#description').focus();
                return false;
            } 
            else if (querystring === "")
            {
                alert('Please enter the SQL');
                $('#querystring').focus();
                return false;
            }
        else{ 
        
            
            
        
          $.ajax({
                type: 'POST',
		url: finalURL,	
		data: {name:name,description:description,querystring:querystring,addedby:"<?php echo $_SESSION['authUserID'];?>",addeddate:"<?php echo date('Y-m-d');?>"},
		
		success: function(response)
		{                         
                                    alert(response);     
                    // window.opener.location.reload();
//window.close()    

$("#divQueryList").val('');
parent.frames['RTop'].location.reload();
                     
                    

                                            

                     
                    	},
		failure: function(response)
		{
			alert("error");
		}		
        });	      
        
        }
    }    
</script>   
<?php
function showdetails()
{
    
 $getQueryFields=sqlStatement("select q.id,q.name,q.description,q.querystring,u.username from   tbl_allcare_query q inner join users u on u.id=q.addedby order by q.id");
 $sqlGroupRows = sqlNumRows($getQueryFields);

if($sqlGroupRows>0)
{
  echo "<table border=0   cellpadding=1 cellspacing=0 width='100% !important;'  style='width:100%;border: 1px #000000 solid;'>";
 echo " <tr height='24' style='background:lightgrey;'>
            <td width='20%' class='bold borderclass'>Query Name</td>
            <td width='20%' class='bold borderclass'>Description</td>
            <td  class='bold borderclass'>SQL</td>
            <td width='15%' class='bold borderclass'>Added By</td>
            <td width='10%' class='bold borderclass'>Delete</td>
            
        </tr>" ;
         while($rowGroup=sqlFetchArray($getQueryFields))
         {
             echo "<tr  style='background:white' height='24'>
                <td class='text borderclass'>".$rowGroup['name']."</td>
                <td width='200px' class='text borderclass'>".$rowGroup['description']."</td>
                <td width='200px' class='text borderclass'>".$rowGroup['querystring']."</td>
                <td class='text borderclass'>".$rowGroup['username']."</td>
                <td class='text borderclass' onclick='deleteRow(".$rowGroup['id'].")'  style=cursor:pointer;>Delete</td>
            </tr>";
         }  
         
echo "</table>";         

}   
else echo "<h4 align='center'>No Data Added</h4>";

//sqlClose();
}
?>
<div id='divQueryList' width="100% !important;">
    
    <input type="button" name="btnAddNew" id="btnAddNew" value="New" style="float: right !important;" onclick="showAddMode('Y');">
    <p>&nbsp;</p>
  <?php 
  showdetails();
?>
         

           
</div>

<div id='divNewQuery' style='display:none'>
    
    <form name="frmAddQuery" method="post">
        <table border="0" width="100%">
            <tr>
                <td width="40%" class="bold">Name :</td>
                <td width="60%" ><input type="text" name="name" id="name" value="" size="20" onchange="emailValidation(this.value);"></td>
            </tr>
            <tr>
                <td  width="40%" class="bold">Description :</td>
             <td width="60%" ><input type="text" name="description" id="description" value="" size="20"></td>
            </tr>   
            <tr>
                <td valign="top" class="bold">SQL :</td>
                <td valign="top"><textarea name="querystring" id="querystring" rows="20" cols="30"></textarea></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="button" name="btnSave" value="Save" onclick="SaveQueryData();" >
                    <input type="button" name="btnCancel" value="Cancel" onclick="showAddMode('N');">
                </td>
                
            </tr>   
        </table>
    </form>
    
</div>

<?php

/*
if(isset($_POST['btnSave']))
{
    //print_r($_POST);
    //[name] => dfd [description] => dsfsd [querystring] => fdfssdf [btnSave] => Save )
    $sqlQuery ="insert into tbl_allcare_query(name,description,querystring) values ('".$_POST['name']."','".$_POST['description']."','".$_POST['querystring']."')";
    //echo $sqlQuery;die;
    
    $insertQueryFields=sqlStatement($sqlQuery);
    echo "<script>showAddMode('N')</script>";
}*/
?>


