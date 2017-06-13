<?php
require_once("../../globals.php");
?>
<html>
    <head>
        <title>Patient Insurance History</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1">
        <link rel="stylesheet" href="../../../library/bootstrap/docs/css/bootstrap-3.2.0.min.css"/>
        <link rel="stylesheet" href="../../../library/datatables_responsive/css/dataTables.bootstrap.min.css"/>
        <link rel="stylesheet" href="../../../library/datatables_responsive/css/responsive.bootstrap.min.css"/>
        <link rel="stylesheet" href="../../../library/datatables_responsive/css/dataTables.tableTools.css"/>
        <link rel="stylesheet" href="css/patientlist.css"/>
        <script src="../../../library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
    </head>
    <body>
     <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header">
                        <h3>Patient Insurance History</h3>
                </div>
                    <?php
                    $pid = $_REQUEST['pid'];
                        echo "<table id='Patientinsurancehistory' class='table table-striped table-bordered dt-responsive nowrap' width='100%'>";
                            $get_patient_insu_header = sqlStatement("SHOW COLUMNS FROM tbl_patient_insurance_history");
                            echo "<thead><tr>";
                            while($set_patient_insu_header = sqlFetchArray($get_patient_insu_header) ){
                                if($set_patient_insu_header['Field'] !== 'pid')
                                    echo "<th>".str_replace("_"," ",(ucwords($set_patient_insu_header['Field'])))."</th>";
                            }
                            echo "</tr></thead>";
                            $get_patient_insu_data = sqlStatement("SELECT * FROM tbl_patient_insurance_history WHERE pid = '$pid'");
                            while($set_patient_insu_data = sqlFetchArray($get_patient_insu_data) ){
                                echo "<tr>";
                                foreach($set_patient_insu_data as $pkey => $pvalue){
                                    if($pkey !== 'pid')
                                        echo "<td>".ucwords($pvalue)."</td>";
                                }
                                echo "</tr>";
                            }
                        echo "</table>";
                        ?>
            </div>
        </div>
     </div>
    <script src="../../../library/bootstrap/docs/js/bootstrap-3.2.0.min.js"></script>
    <script src="../../../library/datatables_responsive/js/jquery.dataTables.min.js"></script>
    <script src="../../../library/datatables_responsive/js/dataTables.bootstrap.min.js"></script>
    <script src="../../../library/datatables_responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../../library/datatables_responsive/js/dataTables.tableTools.js"></script>
    <script src="js/patientsList.js"></script>
    </body>
</html>