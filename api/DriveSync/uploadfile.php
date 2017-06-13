
<?php 
    require_once("../../interface/globals.php");
  

?>
<html>
    <head>
        
    </head>
    <body>
        <script type='text/javascript' src='../../interface/main/js/jquery-1.11.1.min.js'></script><!--
<script>
   $(document).ready(function() {
       
        var obj = {name:'uploads/encounters-report.csv',mimeType:'application/vnd.ms-excel',tmp_name:'/tmp/phpHBw2cH',error:0,size:671714};
        $.ajax({
            type: 'POST',
            url: "http://devint.coopsuite.com/DriveSync/uploadfile/bhavya.enn@gmail.com",	
            data:{obj:obj},
            success: function(response)
            {
                alert(response);
            },
            failure: function(response)
            {
                alert("error"); 
            }		
        });	
 });   
</script> -->
<form METHOD=POST ENCTYPE="multipart/form-data" name='file-data' id='file-data' ACTION="http://qa2allcare.texashousecalls.com/api/DriveSync/uploadfile_web/bhavyae@smartmbbs.com/0B_4Ba3GYJzSsQzBKZ0RseG1DS1U/facility"> 
    File to upload: <INPUT TYPE=FILE NAME="file12131[]" id="file12131" multiple="multiple">
<!--    Folder upload:<input type="file" name="files[]" id="files" multiple="" directory="" webkitdirectory="" mozdirectory="">-->
    <input type="submit" value="submit" id="submit" />
</form>
        <script>
         $(document).ready(function() {
            
              $('input[type="submit"]').click(function(){
                  // alert($('#file12131').val());
          var params = $("#file-data").serialize();
          //alert(params);
           
        });  });
        </script>  
    </body> 
</html>

