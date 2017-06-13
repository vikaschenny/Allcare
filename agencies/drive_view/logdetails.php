<?php
 include("../session_file.php");
?>
<html>
    <head>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css"/>
        <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.1.0/css/responsive.bootstrap.min.css"/>
        <script src="//code.jquery.com/jquery-1.12.3.js"></script>
        <script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
        <script src="//cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js"></script>
        <script src="//cdn.datatables.net/responsive/2.1.0/js/responsive.bootstrap.min.js"></script>
        <script>
         var useremal = '<?php echo $_REQUEST['email']; ?>';
         var user = '<?php echo $_REQUEST['user']; ?>';
        $(document).ready(function() {
            
                $('#example').DataTable( {
                    "ajax": "log_data.php?email="+useremal+"&user="+user,
                    "columns": [
                        { "data": "email" },
                        { "data": "date" },
                        { "data": "user" },
                        { "data": "patient_id" },
                        { "data": "google_folder" }, 
                        { "data": "file_name" },
                        { "data": "status" },
                        { "data": "watsID" }
                    ]
                } );
        
        } );
        </script>
        <style>
            body{
                overflow-x:hidden;
            }
        </style>
    </head>
    <body>
        <table id='example' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Date</th>
                    <th>User</th>
                    <th>ID</th>
                    <th>Google FolderLink</th>
                    <th>File Name</th>
                    <th>Status</th>
                    <th>WatsID</th>
                </tr>
            </thead>
        </table>
</body>
</html>