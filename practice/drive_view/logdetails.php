<?php

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

//continue session
session_start();

//landing page definition -- where to go if something goes wrong
$landingpage = "../index.php?site=".$_SESSION['site_id'];	


if ( isset($_SESSION['portal_username']) ) {    
    $portal_user = $_SESSION['portal_username']; 
}else {
    session_destroy();
    header('Location: '.$landingpage.'&w');
    exit;
} 

$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');
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
                        { "data": "category" }
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
                    <th>Category</th>
                </tr>
            </thead>
        </table>
</body>
</html>
