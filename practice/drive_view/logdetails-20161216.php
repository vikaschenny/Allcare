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
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css"/>
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.0/css/responsive.bootstrap.min.css"/>
        <script src="//code.jquery.com/jquery-1.12.3.js"></script>
        <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.1.0/js/responsive.bootstrap.min.js"></script>
        <script>
        $(document).ready(function() {
            $('#example').DataTable();
        } );
        </script>
        <style>
            body{
                overflow-x:hidden;
            }
        </style>
    </head>
    <body>
        <?php $sql=sqlStatement("select * from DriveSync_log where email='".$_REQUEST['email']."' and user='".$_REQUEST['user']."' order by id desc ");
    
                echo "<table id='example' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>"
            . "         <thead>"
                        . "<tr>"
                        . "<th>Email</th>"
                        . "<th>Date</th>"
                        . "<th>User</th>"
                        . "<th> ID </th>"
                        . "<th>Google FolderLink</th>"
                        . "<th>File Name</th>"
                        . "<th>Status</th>"
                        . "<th>WatsID</th>"
                        . "</tr>"
                        . "</thead><tbody>";
                while($data_row=sqlFetchArray($sql)){
                    
                    echo "<tr >"
                    . "<td >".$data_row['email']."</td>";
                    echo "<td >".$data_row['date']."</td>";
                    echo "<td >".$data_row['user']."</td>";
                    echo "<td >".$data_row['patient_id']."</td>";
                    if(strpos($data_row['google_folder'], '||')==false){
                         echo "<td ><a href=".$data_row['google_folder']." target='_blank'>".$data_row['google_folder']."</a></td>";
                    }
                    else{
                       $arr= explode("||",$data_row['google_folder']);
                        echo "<td >";
                       foreach($arr as $linkval){
                          echo"<a href=".$linkval." target='_blank'>".$linkval."</a>"; echo "<br>";
                       }
                       echo "</td>";
                    }
                   
                    echo "<td >".$data_row['file_name']."</td>";
                    echo "<td >".$data_row['status']."</td>";
                    if($data_row['watsID']!=0)
                    echo "<td ><a href ='https://devint.coopsuite.com/wp-admin/post.php?post=".$data_row['watsID']."&action=edit' target='_blank'>".$data_row['watsID']."</a></td></tr>";
                    else
                    echo "<td >".$data_row['watsID']."</a></td></tr>";
                }
                
                echo"</tbody></table>"; ?>
    
</body>
</html>