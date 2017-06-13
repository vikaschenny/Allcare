<link rel="stylesheet" type="text/css" href="DataTables-1.9.4/media/css/demo_page.css" media="screen" />
<link rel="stylesheet" type="text/css" href="DataTables-1.9.4/media/css/demo_table_jui.css" media="screen" />
<link rel="stylesheet" type="text/css" href="DataTables-1.9.4/extras/TableTools/media/css/TableTools.css" media="screen" />
<link rel="stylesheet" type="text/css" href="DataTables-1.9.4/examples/examples_support/themes/smoothness/jquery-ui-1.8.4.custom.css" media="screen" />


<script type="text/javascript" language="javascript" src="DataTables-1.9.4/media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="DataTables-1.9.4/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="DataTables-1.9.4/examples/examples_support/jquery-ui-tabs.js"></script>
<script type="text/javascript" language="javascript" src="DataTables-1.9.4/examples/examples_support/jquery.jeditable.js"></script>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
         //       $('#tblsqllist').dataTable();
         
          var oTable = $('#tblsqllist').dataTable( {
                        "bJQueryUI": true,
                        "aaSorting": [],
                        "iDisplayLength" :10,
                        "bLengthChange":false,
                        "aoColumns": [ 
			/* nm */   null,
			/* desc */  null,
                                                /* sql */  null,
                                                /* added by */  null,
                                                /* delete  */  { "bSearchable":    false,"bSortable":    false }
			
			
		] 
                    } );
        /* Apply the jEditable handlers to the table */
    oTable.$('td.editable_class').editable( 'sql_editable.php', {
        "callback": function( sValue, y ) {
            var aPos = oTable.fnGetPosition( this );
            oTable.fnUpdate( sValue, aPos[0], aPos[1] );
        },
        "submitdata": function ( value, settings ) {
            return {
                "row_id": this.parentNode.getAttribute('id'),
                "column": oTable.fnGetPosition( this )[2]
            };
        },
        "height": "20px",
        "width": "100%"
    } );
                        

        } );
</script>



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

<div id='divQueryList' width="100% !important;">
    
    <input type="button" name="btnAddNew" id="btnAddNew" value="New" style="float: right !important;" onclick="showAddMode('Y');">
    <p>&nbsp;</p>
  <?php 
    
 $getQueryFields=sqlStatement("select q.id,q.name,q.description,q.querystring,u.username from   tbl_allcare_query q inner join users u on u.id=q.addedby order by q.id");
 $sqlGroupRows = sqlNumRows($getQueryFields);

if($sqlGroupRows>0)
{
    echo "<p>(Note: After Inline Editing please hit 'Enter' to save record.)</p>";
    ?>
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="tblsqllist" width="100%">
	<thead>
		<tr style="font-size: 10pt;background:lightgrey;">
			<th valign="top"><b>Query Name</b></th>
			<th valign="top"><b>Description</b></th>
			<th valign="top"><b>SQL</b></th>
			<th valign="top"><b>Added By</b></th>
			<th valign="top"><b>Delete</b></th>
                                                
		</tr>
	</thead><tbody>

<?php   
$flg=1;
         while($rowGroup=sqlFetchArray($getQueryFields))
         {
             if($flg%2==0) 
             { 
                 $class= "gradeU even";
                 
             }
             else 
             {
                 $class = "gradeU odd"; 
                 
             }
             $flg++;
                
             echo "<tr  id=".$rowGroup['id']." class='$class'  height='24'>
                <td width='120px'>".$rowGroup['name']."</td>
                <td name='description' class='editable_class'  width='200px'>".$rowGroup['description']."</td>
                <td name='querystring' class='editable_class' width='250px'>".$rowGroup['querystring']."</td>
                <td width='100px'>".$rowGroup['username']."</td>
                <td  onclick='deleteRow(".$rowGroup['id'].")'  style=cursor:pointer;>Delete</td>
                 
            </tr>";
             
         }  
         
echo "</tbody></table>";         

}   
else echo "<h4 align='center'>No Data Added</h4>";
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


